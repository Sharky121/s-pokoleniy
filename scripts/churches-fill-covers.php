<?php
/**
 * Раздел «Поддержка РПЦ» (churches): по title ищет картинку в интернете (Wikimedia Commons),
 * сохраняет в public/images, пишет путь в cover в БД. Картинки не повторяются.
 * Запуск: php scripts/churches-fill-covers.php [--all]
 *   --all — обработать все записи (по умолчанию только с пустым cover)
 */
$base = dirname(__DIR__);
require $base . '/vendor/autoload.php';
$app = require_once $base . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$imagesDir = $base . '/public/images';
$processAll = in_array('--all', array_slice($argv, 1), true);

/** Поиск изображения в Wikimedia Commons по заголовку. Не повторяет URL из $usedUrls. */
function findCommonsImageByTitle(string $title, array &$usedUrls): ?string
{
    $query = trim(preg_replace('/\s+/', ' ', $title));
    if ($query === '') {
        return null;
    }
    $apiUrl = 'https://commons.wikimedia.org/w/api.php?' . http_build_query([
        'action' => 'query',
        'generator' => 'search',
        'gsrnamespace' => 6,
        'gsrsearch' => $query,
        'gsrlimit' => 30,
        'prop' => 'imageinfo',
        'iiprop' => 'url|mime',
        'iiurlwidth' => 800,
        'format' => 'json',
    ]);
    $ctx = stream_context_create([
        'http' => ['timeout' => 15, 'user_agent' => 'Mozilla/5.0 (compatible; SvyazPokolenij/1.0; +https://s-pokoleniy.ru)'],
    ]);
    $json = @file_get_contents($apiUrl, false, $ctx);
    if ($json === false) {
        return null;
    }
    $data = json_decode($json, true);
    if (empty($data['query']['pages'])) {
        return null;
    }
    $usedSet = array_flip($usedUrls);
    foreach ($data['query']['pages'] as $page) {
        $info = $page['imageinfo'][0] ?? null;
        if (!$info) {
            continue;
        }
        $mime = $info['mime'] ?? '';
        if (stripos($mime, 'svg') !== false) {
            continue;
        }
        $url = $info['thumburl'] ?? $info['url'] ?? null;
        if ($url && !isset($usedSet[$url]) && (stripos($mime, 'jpeg') !== false || stripos($mime, 'jpg') !== false || stripos($mime, 'png') !== false)) {
            return $url;
        }
    }
    return null;
}

/** Fallback-URL для раздела церквей (без повторов). */
$churchFallbackUrls = [
    'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/Russian_Orthodox_Church_in_Tallinn.jpg/800px-Russian_Orthodox_Church_in_Tallinn.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Blessed_Virgin_of_Vladmir_Russian_Orthodox_Church%2C_Rocklea%2C_2007.jpg/800px-Blessed_Virgin_of_Vladmir_Russian_Orthodox_Church%2C_Rocklea%2C_2007.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Bryansk%2C_Bryansk_Oblast%2C_Russia_-_panoramio.jpg/800px-Bryansk%2C_Bryansk_Oblast%2C_Russia_-_panoramio.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Aelia_Capitolina_%2815087534494%29.jpg/800px-Aelia_Capitolina_%2815087534494%29.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/Church_of_St._Nicholas_in_Khamovniki_01.jpg/800px-Church_of_St._Nicholas_in_Khamovniki_01.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Moscow_July_2011-14a.jpg/800px-Moscow_July_2011-14a.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/Saint_Basil%27s_Cathedral_at_sunset.jpg/800px-Saint_Basil%27s_Cathedral_at_sunset.jpg',
];

/** Скачать по URL в файл. Возвращает true при успехе. */
function downloadToFile(string $url, string $filepath): bool
{
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 25,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'header' => 'Referer: https://commons.wikimedia.org/',
        ],
    ]);
    $data = @file_get_contents($url, false, $ctx);
    if ($data === false || strlen($data) < 5000) {
        return false;
    }
    return file_put_contents($filepath, $data) !== false;
}

/** Уникальное имя файла: church-{id}.jpg */
function coverFilename(int $id): string
{
    return "church-{$id}.jpg";
}

$churches = \App\Models\Church::orderBy('id')->get(['id', 'title', 'cover']);
if (!$processAll) {
    $churches = $churches->filter(function ($c) {
        return empty($c->cover) || trim($c->cover) === '';
    });
}

if ($churches->isEmpty()) {
    echo "Нет записей для обработки (используйте --all для обработки всех).\n";
    exit(0);
}

if (!is_dir($imagesDir)) {
    mkdir($imagesDir, 0755, true);
}

$usedUrls = [];
$updated = [];
$failed = [];

foreach ($churches as $church) {
    $title = $church->title ?? '';
    $url = findCommonsImageByTitle($title, $usedUrls);
    if (!$url) {
        $url = findCommonsImageByTitle('православный храм церковь Россия', $usedUrls);
    }
    if (!$url) {
        $usedSet = array_flip($usedUrls);
        foreach ($churchFallbackUrls as $fb) {
            if (!isset($usedSet[$fb])) {
                $url = $fb;
                break;
            }
        }
    }
    if (!$url) {
        $failed[] = $church->id . ': ' . mb_substr($title, 0, 50);
        continue;
    }

    $usedUrls[] = $url;
    $filename = coverFilename($church->id);
    $filepath = $imagesDir . '/' . $filename;

    if (!downloadToFile($url, $filepath)) {
        // Повтор с placeholder, чтобы у записи была обложка
        $placehold = 'https://placehold.co/800x600.jpg?text=' . rawurlencode(mb_substr($title, 0, 30));
        if (downloadToFile($placehold, $filepath)) {
            $coverPath = '/images/' . $filename;
            $church->cover = $coverPath;
            $church->save();
            $updated[] = $church->id . ' — ' . $coverPath . ' (placeholder)';
            echo "OK (placeholder): id={$church->id} " . mb_substr($title, 0, 45) . " → /images/{$filename}\n";
        } else {
            $failed[] = $church->id . ': ' . mb_substr($title, 0, 50) . ' (ошибка загрузки)';
            array_pop($usedUrls);
        }
        continue;
    }

    $coverPath = '/images/' . $filename;
    $church->cover = $coverPath;
    $church->save();
    $updated[] = $church->id . ' — ' . $coverPath;
    echo "OK: id={$church->id} " . mb_substr($title, 0, 45) . " → {$coverPath}\n";
}

if (!empty($failed)) {
    echo "\nНе удалось: " . implode("\n  ", $failed) . "\n";
}

echo "\nОбновлено записей: " . count($updated) . "\n";

if (count($updated) > 0) {
    echo "Добавьте в git:\n  git add public/images/church-*.jpg\n  git commit -m \"Обложки раздел Поддержка РПЦ\"\n";
}
