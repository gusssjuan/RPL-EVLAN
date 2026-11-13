<?php

declare(strict_types=1);

$file = $argv[1] ?? null;

if ($file === null || ! is_file($file)) {
    fwrite(STDERR, "Pemakaian: php scripts/logo-color.php <berkas-logo>\n");
    exit(1);
}

$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

if ($extension === 'svg') {
    $contents = (string) file_get_contents($file);
    preg_match_all('/fill="(#[0-9a-fA-F]{3,8})"/', $contents, $matches);
    $colors = $matches[1] ?? [];
} else {
    if (! extension_loaded('gd')) {
        fwrite(STDERR, "Ekstensi GD tidak aktif.\n");
        exit(1);
    }

    $image = imagecreatefrompng($file);

    if ($image === false) {
        fwrite(STDERR, "Gagal membaca berkas gambar.\n");
        exit(1);
    }

    $colors = [];

    for ($x = 0; $x < imagesx($image); $x += 2) {
        for ($y = 0; $y < imagesy($image); $y += 2) {
            $rgba = imagecolorat($image, $x, $y);
            $alpha = ($rgba >> 24) & 0x7F;

            if ($alpha > 32) {
                continue;
            }

            $colors[] = sprintf('#%06X', $rgba & 0xFFFFFF);
        }
    }

    imagedestroy($image);
}

if ($colors === []) {
    fwrite(STDERR, "Tidak ada warna yang ditemukan.\n");
    exit(1);
}

$counts = array_count_values($colors);
arsort($counts);
$dominant = array_key_first($counts);

[$r, $g, $b] = sscanf($dominant, '#%02x%02x%02x');
$channel = static function (int $value): float {
    $value /= 255;

    return $value <= 0.04045 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
};
$luminance = 0.2126 * $channel($r) + 0.7152 * $channel($g) + 0.0722 * $channel($b);
$contrast = 1.05 / ($luminance + 0.05);

printf("Warna dominan: %s\n", $dominant);
printf("Kontras terhadap putih: %.2f:1\n", $contrast);
printf("Lolos teks normal (4,5:1): %s\n", $contrast >= 4.5 ? 'ya' : 'tidak');
