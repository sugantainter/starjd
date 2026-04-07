<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class MigrateToGcs extends Command
{
    protected $signature = 'storage:migrate-to-gcs
                            {--disk=gcs : Target filesystem disk (must be configured in config/filesystems.php)}
                            {--dry-run : List actions without uploading}
                            {--force : Overwrite objects that already exist on the target disk}
                            {--delete-local : Delete each local file after upload, or immediately if it already exists on the target disk (asks for confirmation)}
                            {--no-private : Do not scan storage/app/private (default local disk)}';

    protected $description = 'Upload existing files from local storage (public + private) to Google Cloud Storage';

    public function handle(): int
    {
        $targetDisk = (string) $this->option('disk');
        $disks = config('filesystems.disks', []);

        if (! isset($disks[$targetDisk])) {
            $this->error("Disk [{$targetDisk}] is not defined in config/filesystems.php.");

            return self::FAILURE;
        }

        if (($disks[$targetDisk]['driver'] ?? '') !== 'gcs' && ! $this->confirm("Disk [{$targetDisk}] is not the gcs driver. Continue anyway?", false)) {
            return self::FAILURE;
        }

        $bucket = config("filesystems.disks.{$targetDisk}.bucket");
        if (empty($bucket)) {
            $this->error('Target disk bucket is empty. Set GOOGLE_CLOUD_STORAGE_BUCKET (and related .env values).');

            return self::FAILURE;
        }

        if ($this->option('delete-local') && $this->option('dry-run')) {
            $this->error('--delete-local cannot be combined with --dry-run.');

            return self::FAILURE;
        }

        if ($this->option('delete-local') && ! $this->confirm('Local files will be removed after upload, or right away when the object already exists remotely. Continue?', false)) {
            return self::FAILURE;
        }

        $roots = $this->localRoots();
        if ($roots->isEmpty()) {
            $this->warn('No local storage directories found (storage/app/public or storage/app/private).');

            return self::SUCCESS;
        }

        $this->info("Target: disk [{$targetDisk}] → bucket [{$bucket}]");
        if ($prefix = config("filesystems.disks.{$targetDisk}.path_prefix")) {
            $this->line("Path prefix: {$prefix}");
        }

        $files = $this->collectFiles($roots);
        $total = $files->count();

        if ($total === 0) {
            $this->info('No files to migrate.');

            return self::SUCCESS;
        }

        $this->info("Found {$total} file(s) to process.");
        $dry = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $deleteLocal = (bool) $this->option('delete-local');

        $uploaded = 0;
        $skipped = 0;
        $failed = 0;
        $deleted = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($files as $row) {
            /** @var array{path: string, absolute: string, source: string} $row */
            $relative = $row['path'];
            $absolute = $row['absolute'];

            if ($dry) {
                $bar->advance();

                continue;
            }

            try {
                if (! $force && Storage::disk($targetDisk)->exists($relative)) {
                    $skipped++;
                    if ($deleteLocal && @unlink($absolute)) {
                        $deleted++;
                    }
                    $bar->advance();

                    continue;
                }

                $contents = @file_get_contents($absolute);
                if ($contents === false) {
                    throw new \RuntimeException('Could not read file');
                }

                $mime = @mime_content_type($absolute) ?: 'application/octet-stream';

                $ok = Storage::disk($targetDisk)->put($relative, $contents, [
                    'visibility' => 'public',
                    'metadata' => ['contentType' => $mime],
                ]);

                if (! $ok) {
                    throw new \RuntimeException('Storage upload failed (disk returned false).');
                }

                $uploaded++;

                if ($deleteLocal && @unlink($absolute)) {
                    $deleted++;
                }
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->error("Failed [{$relative}]: ".$e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($dry) {
            $this->info('Dry run only — no uploads performed.');
            $files->take(50)->each(function (array $row) {
                $this->line("  [{$row['source']}] {$row['path']}");
            });
            if ($total > 50) {
                $this->line('  … and '.($total - 50).' more');
            }

            return self::SUCCESS;
        }

        $this->info("Uploaded: {$uploaded}");
        if ($skipped > 0) {
            $this->info("Skipped upload (object already on {$targetDisk}): {$skipped}".($force ? '' : ' — use --force to re-upload'));
        }
        if ($deleted > 0) {
            $this->info("Deleted local file(s): {$deleted}");
        }
        if ($failed > 0) {
            $this->error("Failed: {$failed}");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @return LazyCollection<int, array{path: string, absolute: string, source: string}>
     */
    private function collectFiles(LazyCollection $roots): LazyCollection
    {
        /** @var array<string, array{path: string, absolute: string, source: string}> $byPath */
        $byPath = [];

        foreach ($roots as $root) {
            $label = $root['label'];
            $basePath = $root['path'];

            if (! is_dir($basePath)) {
                continue;
            }

            $finder = Finder::create()
                ->files()
                ->in($basePath)
                ->ignoreUnreadableDirs();

            /** @var SplFileInfo $file */
            foreach ($finder as $file) {
                $relative = str_replace('\\', '/', $file->getRelativePathname());
                if ($relative === '') {
                    continue;
                }

                if (isset($byPath[$relative])) {
                    $prev = $byPath[$relative]['source'];
                    $this->warn("Duplicate relative path (later wins): {$relative} [{$prev}] ← [{$label}]");
                }

                $byPath[$relative] = [
                    'path' => $relative,
                    'absolute' => $file->getRealPath(),
                    'source' => $label,
                ];
            }
        }

        return LazyCollection::make(array_values($byPath));
    }

    /**
     * @return LazyCollection<int, array{label: string, path: string}>
     */
    private function localRoots(): LazyCollection
    {
        $items = [
            ['label' => 'public', 'path' => storage_path('app/public')],
        ];

        if (! $this->option('no-private')) {
            $items[] = ['label' => 'private', 'path' => storage_path('app/private')];
        }

        return LazyCollection::make($items)->filter(fn (array $r) => is_dir($r['path']));
    }
}
