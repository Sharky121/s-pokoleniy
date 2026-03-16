<?php
/**
 * Проверка путей обложек (art.cover) и существования файлов.
 * Запуск: php scripts/check-cover-paths.php (из корня проекта, после bootstrap Laravel не нужен — только БД).
 * Или: docker compose -p spokoleniy exec -T app php /var/www/html/scripts/check-cover-paths.php
 */
$base = dirname(__DIR__);
require $base . '/vendor/autoload.php';
$app = require_once $base . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$items = \App\Models\Art::orderBy('id')->get(['id', 'title', 'cover']);
$publicPath = $base . '/public';
$storagePath = $base . '/storage/app/public';

echo "ID | Путь в БД (cover) | Файл существует? | Название\n";
echo str_repeat('-', 120) . "\n";

foreach ($items as $a) {
    $path = $a->cover;
    $title = mb_substr($a->title ?? '', 0, 45);
    if (!$path) {
        echo "{$a->id} | (пусто) | - | {$title}\n";
        continue;
    }
    $full = null;
    if (strpos($path, '/storage/') === 0) {
        $full = rtrim($storagePath, '/') . '/' . ltrim(substr($path, 9), '/');
    } elseif (strpos($path, '/images/') === 0) {
        $full = $publicPath . $path;
    } else {
        $full = $publicPath . (strpos($path, '/') === 0 ? $path : '/' . $path);
    }
    $exists = file_exists($full) ? 'ДА' : 'НЕТ';
    echo "{$a->id} | {$path} | {$exists} | {$title}\n";
}
