<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MigrateToGcs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-to-gcs {--dry-run : Only list files without uploading}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all local public storage files to Google Cloud Storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourceDisk = 'public';
        $targetDisk = config('filesystems.default'); // Should be gcs

        if ($targetDisk !== 'gcs' && !$this->confirm("The default disk is not GCS (it is $targetDisk). Proceed anyway?")) {
            return self::FAILURE;
        }

        $this->info("Scanning local public storage...");
        
        $localPath = storage_path('app/public');
        if (!File::exists($localPath)) {
            $this->error("Local public storage path not found: $localPath");
            return self::FAILURE;
        }

        $files = File::allFiles($localPath);
        $total = count($files);

        if ($total === 0) {
            $this->info("No files found to migrate.");
            return self::SUCCESS;
        }

        $this->info("Found $total files. Starting migration to $targetDisk...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname();
            
            if ($this->option('dry-run')) {
                $this->line("\n[DRY RUN] Would upload: $relativePath");
                $bar->advance();
                continue;
            }

            try {
                // Upload to GCS
                $content = File::get($file->getRealPath());
                $mimeType = File::mimeType($file->getRealPath());

                $uploaded = Storage::disk($targetDisk)->put($relativePath, $content, [
                    'visibility' => 'public',
                    'ContentType' => $mimeType
                ]);

                if ($uploaded) {
                    $successCount++;
                } else {
                    $this->error("\nFailed to upload: $relativePath");
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $this->error("\nError uploading $relativePath: " . $e->getMessage());
                $errorCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($this->option('dry-run')) {
            $this->info("Dry run complete. No files were uploaded.");
        } else {
            $this->info("Migration complete!");
            $this->info("Successfully uploaded: $successCount");
            if ($errorCount > 0) {
                $this->error("Failed uploads: $errorCount");
            }
        }

        return $errorCount === 0 ? self::SUCCESS : self::FAILURE;
    }
}
