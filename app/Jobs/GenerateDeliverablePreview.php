<?php

namespace App\Jobs;

use App\Models\Collaboration;
use App\Services\DeliverablePreviewGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateDeliverablePreview implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public int $tries = 1;

    public function __construct(public int $collaborationId) {}

    public function handle(DeliverablePreviewGenerator $generator): void
    {
        $collab = Collaboration::find($this->collaborationId);
        if (! $collab || ! $collab->deliverable_content) {
            return;
        }

        $disk = Storage::disk('gcs');
        $originalKey = $collab->deliverable_content;

        if (! $disk->exists($originalKey)) {
            Log::warning('GenerateDeliverablePreview: original missing', ['id' => $this->collaborationId]);
            $collab->update(['deliverable_preview_status' => 'failed']);

            return;
        }

        if (! $this->isVideoPath($originalKey)) {
            $collab->update([
                'deliverable_preview_path' => null,
                'deliverable_preview_status' => 'ready',
            ]);

            return;
        }

        $tmpIn = tempnam(sys_get_temp_dir(), 'starjd_in_');
        $tmpOut = tempnam(sys_get_temp_dir(), 'starjd_out_').'.mp4';

        try {
            $read = $disk->readStream($originalKey);
            if (! is_resource($read)) {
                throw new \RuntimeException('Could not read original from storage');
            }
            file_put_contents($tmpIn, stream_get_contents($read));
            fclose($read);

            $generator->generate($tmpIn, $tmpOut);

            $dest = 'project_deliverables/previews/'.$collab->id.'_'.Str::lower(Str::random(10)).'.mp4';
            $disk->put($dest, (string) file_get_contents($tmpOut), ['visibility' => 'private']);

            $previous = $collab->deliverable_preview_path;
            if ($previous && $previous !== $dest && $disk->exists($previous)) {
                $disk->delete($previous);
            }

            $collab->update([
                'deliverable_preview_path' => $dest,
                'deliverable_preview_status' => 'ready',
            ]);
        } catch (\Throwable $e) {
            Log::error('GenerateDeliverablePreview failed', [
                'collaboration_id' => $this->collaborationId,
                'message' => $e->getMessage(),
            ]);
            $collab->update(['deliverable_preview_status' => 'failed']);
        } finally {
            if (is_file($tmpIn)) {
                @unlink($tmpIn);
            }
            if (is_file($tmpOut)) {
                @unlink($tmpOut);
            }
        }
    }

    private function isVideoPath(string $path): bool
    {
        return (bool) preg_match('/\.(mp4|mov|m4v|avi|mkv)$/i', $path);
    }
}
