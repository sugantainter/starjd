<?php

return [

    /*
    |--------------------------------------------------------------------------
    | FFmpeg binary
    |--------------------------------------------------------------------------
    |
    | Used to create a compressed, watermarked preview video after creators upload.
    | Install: apt install ffmpeg (Debian/Ubuntu)
    |
    */
    'ffmpeg_binary' => env('DELIVERABLE_FFMPEG_BINARY', 'ffmpeg'),

    /*
    |--------------------------------------------------------------------------
    | Watermark (burned into preview transcode only)
    |--------------------------------------------------------------------------
    */
    'watermark_font' => env(
        'DELIVERABLE_WATERMARK_FONT',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf'
    ),

    'watermark_text' => env('DELIVERABLE_WATERMARK_TEXT', 'STARJD PREVIEW'),

    /*
    |--------------------------------------------------------------------------
    | Preview transcode
    |--------------------------------------------------------------------------
    |
    | Max height in pixels (width scales). CRF / bitrate caps keep files small
    | for brand preview before project completion.
    |
    */
    'preview_max_height' => (int) env('DELIVERABLE_PREVIEW_MAX_HEIGHT', 480),

    'preview_crf' => (int) env('DELIVERABLE_PREVIEW_CRF', 28),

    'preview_max_video_bitrate' => env('DELIVERABLE_PREVIEW_MAXRATE', '2M'),

    'preview_audio_bitrate' => env('DELIVERABLE_PREVIEW_AUDIO_BITRATE', '96k'),

];
