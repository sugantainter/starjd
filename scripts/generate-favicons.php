#!/usr/bin/env php
<?php
/**
 * One-shot: build square favicons from public/logo.png (center crop).
 * Run: php scripts/generate-favicons.php
 */
$public = dirname(__DIR__) . '/public';
$srcPath = $public . '/logo.png';
if (! is_readable($srcPath)) {
    fwrite(STDERR, "Missing or unreadable: $srcPath\n");
    exit(1);
}

$src = @imagecreatefrompng($srcPath);
if (! $src) {
    fwrite(STDERR, "Could not load PNG.\n");
    exit(1);
}
imagesavealpha($src, true);

$sw = imagesx($src);
$sh = imagesy($src);
$side = min($sw, $sh);
$sx = (int) (($sw - $side) / 2);
$sy = (int) (($sh - $side) / 2);

function saveSize($src, int $sx, int $sy, int $side, int $size, string $path): void
{
    $dst = imagecreatetruecolor($size, $size);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefilledrectangle($dst, 0, 0, $size, $size, $transparent);
    imagecopyresampled($dst, $src, 0, 0, $sx, $sy, $size, $size, $side, $side);
    imagepng($dst, $path, 9);
    imagedestroy($dst);
}

$sizes = [16, 32, 48, 180, 192, 512];
foreach ($sizes as $size) {
    saveSize($src, $sx, $sy, $side, $size, $public . '/favicon-' . $size . 'x' . $size . '.png');
}

imagedestroy($src);

echo "Wrote favicon-*x*.png in public/\n";
