<?php

$directory = __DIR__.'/../public/assets/img/pwa';
if (! is_dir($directory)) {
    mkdir($directory, 0755, true);
}

$source = imagecreatefrompng(__DIR__.'/../public/assets/img/logo_smkn_2.png');
foreach ([180 => 'apple-touch-icon.png', 192 => 'icon-192.png', 512 => 'icon-512.png'] as $size => $filename) {
    $icon = imagecreatetruecolor($size, $size);
    imagefill($icon, 0, 0, imagecolorallocate($icon, 255, 255, 255));
    $padding = (int) round($size * 0.1);
    $contentSize = $size - 2 * $padding;
    imagecopyresampled($icon, $source, $padding, $padding, 0, 0, $contentSize, $contentSize, imagesx($source), imagesy($source));
    imagepng($icon, $directory.'/'.$filename);
}
