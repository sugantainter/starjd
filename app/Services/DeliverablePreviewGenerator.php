<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use RuntimeException;

class DeliverablePreviewGenerator
{
    public function generate(string $inputPath, string $outputPath): void
    {
        $ffmpeg = config('deliverables.ffmpeg_binary', 'ffmpeg');
        $h = max(240, min(1080, (int) config('deliverables.preview_max_height', 480)));
        $crf = max(18, min(35, (int) config('deliverables.preview_crf', 28)));
        $maxrate = config('deliverables.preview_max_video_bitrate', '2M');
        $audioBr = config('deliverables.preview_audio_bitrate', '96k');

        $logoPath = config('deliverables.watermark_logo');
        if (! is_string($logoPath) || ! is_file($logoPath)) {
            $logoPath = public_path('logo.png');
        }
        $useLogo = is_file($logoPath);

        $videoPrefix = $useLogo
            ? $this->buildLogoWatermarkArgs($ffmpeg, $inputPath, $logoPath, $h, $crf, $maxrate)
            : $this->buildTextWatermarkArgs($ffmpeg, $inputPath, $h, $crf, $maxrate);

        $result = Process::timeout(3600)->run(array_merge($videoPrefix, [
            '-map', '0:a?',
            '-c:a', 'aac',
            '-b:a', $audioBr,
            $outputPath,
        ]));

        if (! $result->successful()) {
            $result = Process::timeout(3600)->run(array_merge($videoPrefix, [
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

    /**
     * @return list<string>
     */
    private function buildLogoWatermarkArgs(
        string $ffmpeg,
        string $inputPath,
        string $logoPath,
        int $h,
        int $crf,
        string $maxrate
    ): array {
        $logoW = max(80, min(500, (int) config('deliverables.watermark_logo_width', 220)));

        $filterComplex = sprintf(
            '[0:v]scale=-2:%d:flags=lanczos[base];[1:v]scale=%d:-1,format=rgba[lg];[base][lg]overlay=(W-w)/2:(H-h)/2:format=auto[v]',
            $h,
            $logoW
        );

        return [
            $ffmpeg,
            '-y',
            '-hide_banner',
            '-loglevel', 'warning',
            '-i', $inputPath,
            '-i', $logoPath,
            '-filter_complex', $filterComplex,
            '-map', '[v]',
            '-c:v', 'libx264',
            '-preset', 'fast',
            '-crf', (string) $crf,
            '-maxrate', $maxrate,
            '-bufsize', '4M',
            '-movflags', '+faststart',
        ];
    }

    /**
     * @return list<string>
     */
    private function buildTextWatermarkArgs(
        string $ffmpeg,
        string $inputPath,
        int $h,
        int $crf,
        string $maxrate
    ): array {
        $font = config('deliverables.watermark_font');
        $text = config('deliverables.watermark_text', 'STARJD PREVIEW');

        if (! is_file($font)) {
            throw new RuntimeException("Watermark font not found: {$font}");
        }

        $textEsc = str_replace(["\\", "'", ':'], ['\\\\', "\\'", '\\:'], $text);
        $fontEsc = str_replace(["\\", "'", ':'], ['\\\\', "\\'", '\\:'], $font);

        $vf = "scale=-2:{$h}:flags=lanczos,drawtext=fontfile='{$fontEsc}':text='{$textEsc}':fontcolor=white@0.42:fontsize=36:borderw=3:bordercolor=black@0.55:x=(w-text_w)/2:y=(h-text_h)/2";

        return [
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
    }
}
