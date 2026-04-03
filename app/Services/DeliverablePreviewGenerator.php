<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use RuntimeException;

class DeliverablePreviewGenerator
{
    public function generate(string $inputPath, string $outputPath): void
    {
        $ffmpeg = config('deliverables.ffmpeg_binary', 'ffmpeg');
        $font = config('deliverables.watermark_font');
        $text = config('deliverables.watermark_text', 'STARJD PREVIEW');
        $h = max(240, min(1080, (int) config('deliverables.preview_max_height', 480)));
        $crf = max(18, min(35, (int) config('deliverables.preview_crf', 28)));
        $maxrate = config('deliverables.preview_max_video_bitrate', '2M');
        $audioBr = config('deliverables.preview_audio_bitrate', '96k');

        if (! is_file($font)) {
            throw new RuntimeException("Watermark font not found: {$font}");
        }

        $textEsc = str_replace(["\\", "'", ':'], ['\\\\', "\\'", '\\:'], $text);
        $fontEsc = str_replace(["\\", "'", ':'], ['\\\\', "\\'", '\\:'], $font);

        $vf = "scale=-2:{$h}:flags=lanczos,drawtext=fontfile='{$fontEsc}':text='{$textEsc}':fontcolor=white@0.42:fontsize=36:borderw=3:bordercolor=black@0.55:x=(w-text_w)/2:y=(h-text_h)/2";

        $base = [
            $ffmpeg,
            '-y',
            '-hide_banner',
            '-loglevel', 'warning',
            '-i', $inputPath,
            '-vf', $vf,
            '-c:v', 'libx264',
            '-preset', 'fast',
            '-crf', (string) $crf,
            '-maxrate', $maxrate,
            '-bufsize', '4M',
            '-movflags', '+faststart',
        ];

        $result = Process::timeout(3600)->run(array_merge($base, [
            '-c:a', 'aac',
            '-b:a', $audioBr,
            $outputPath,
        ]));

        if (! $result->successful()) {
            $result = Process::timeout(3600)->run(array_merge($base, [
                '-an',
                $outputPath,
            ]));
        }

        if (! $result->successful()) {
            throw new RuntimeException(trim($result->errorOutput() ?: $result->output()) ?: 'ffmpeg failed');
        }

        if (! is_file($outputPath) || filesize($outputPath) < 32) {
            throw new RuntimeException('Preview output missing or empty');
        }
    }
}
