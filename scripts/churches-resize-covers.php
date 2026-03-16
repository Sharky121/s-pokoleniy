<?php
/**
 * Приводит все обложки раздела РПЦ (public/images/church-*.jpg) к единому размеру 800×600.
 * Запуск: php scripts/churches-resize-covers.php
 * Использует Intervention Image (gd/imagick) или, при недоступности, ImageMagick convert.
 */
$base = dirname(__DIR__);
require $base . '/vendor/autoload.php';
$app = require_once $base . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$imagesDir = $base . '/public/images';
$width = 800;
$height = 600;

$files = glob($imagesDir . '/church-*.jpg');
if (empty($files)) {
    echo "Нет файлов church-*.jpg в public/images.\n";
    exit(0);
}

function resizeWithIntervention(string $filepath, int $w, int $h): bool
{
    $driver = extension_loaded('imagick') ? 'imagick' : 'gd';
    try {
        $manager = new \Intervention\Image\ImageManager(['driver' => $driver]);
        $img = $manager->make($filepath);
        $img->fit($w, $h);
        $img->encode('jpg', 88);
        $img->save($filepath);
        return true;
    } catch (\Throwable $e) {
        return false;
    }
}

function resizeWithConvert(string $filepath, int $w, int $h): bool
{
    $tmp = $filepath . '.tmp.' . pathinfo($filepath, PATHINFO_EXTENSION);
    $cmd = sprintf(
        'convert %s -resize %dx%d^ -gravity center -extent %dx%d %s 2>/dev/null && mv -f %s %s',
        escapeshellarg($filepath),
        $w,
        $h,
        $w,
        $h,
        escapeshellarg($tmp),
        escapeshellarg($tmp),
        escapeshellarg($filepath)
    );
    exec($cmd, $out, $code);
    return $code === 0;
}

$ok = 0;
foreach ($files as $filepath) {
    if (resizeWithIntervention($filepath, $width, $height) || resizeWithConvert($filepath, $width, $height)) {
        $ok++;
        echo "OK: " . basename($filepath) . "\n";
    } else {
        echo "Ошибка: " . basename($filepath) . " (нужны GD с JPEG, Imagick или ImageMagick convert)\n";
    }
}
echo "\nОбработано: $ok из " . count($files) . ".\n";
