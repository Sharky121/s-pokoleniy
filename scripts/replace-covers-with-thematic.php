<?php
/**
 * Подбирает тематическую картинку под заголовок каждой записи через поиск в Wikimedia Commons,
 * скачивает и перезаписывает обложку. Только для записей с обложкой в storage.
 * Запуск: php scripts/replace-covers-with-thematic.php
 */
$base = dirname(__DIR__);
require $base . '/vendor/autoload.php';
$app = require_once $base . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$storagePath = $base . '/storage/app/public';

function pathToFull(string $path): ?string {
    if (empty($path) || strpos($path, '/storage/') !== 0) return null;
    global $storagePath;
    return rtrim($storagePath, '/') . '/' . ltrim(substr($path, 9), '/');
}

/**
 * Поиск изображения в Wikimedia Commons по заголовку.
 * Пропускает URL из $usedUrls, чтобы картинки не повторялись.
 * Возвращает URL превью или null.
 */
function findCommonsImageByTitle(string $title, array &$usedUrls): ?string {
    $query = trim(preg_replace('/\s+/', ' ', $title));
    if ($query === '') return null;
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
    $ctx = stream_context_create(['http' => ['timeout' => 15, 'user_agent' => 'Mozilla/5.0 (compatible; SvyazPokolenij/1.0; +https://s-pokoleniy.ru)']]);
    $json = @file_get_contents($apiUrl, false, $ctx);
    if ($json === false) return null;
    $data = json_decode($json, true);
    if (empty($data['query']['pages'])) return null;
    $usedSet = array_flip($usedUrls);
    foreach ($data['query']['pages'] as $page) {
        $info = $page['imageinfo'][0] ?? null;
        if (!$info) continue;
        $mime = $info['mime'] ?? '';
        if (stripos($mime, 'svg') !== false) continue;
        $url = $info['thumburl'] ?? $info['url'] ?? null;
        if ($url && !isset($usedSet[$url]) && (stripos($mime, 'jpeg') !== false || stripos($mime, 'jpg') !== false || stripos($mime, 'png') !== false)) {
            return $url;
        }
    }
    return null;
}

/** Ключевые слова по типу для поиска, если по заголовку ничего не нашли */
$searchFallbackByType = [
    'art' => 'theatre concert culture',
    'news' => 'conference meeting event',
    'church' => 'orthodox church Russia',
    'publishing' => 'book presentation',
    'orphan' => 'children charity',
    'veteran' => 'veteran memorial',
];

/** Несколько fallback-URL по типу, чтобы при отсутствии поиска брать неповторяющиеся */
$fallbackUrlsByType = [
    'art' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Teatro_alla_Scala_Milan_-_panoramio.jpg/800px-Teatro_alla_Scala_Milan_-_panoramio.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/Berthier._Eden_concert_-_btv1b531486965.jpg/800px-Berthier._Eden_concert_-_btv1b531486965.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b8/Concert_at_Arena_di_Verona.jpg/800px-Concert_at_Arena_di_Verona.jpg',
    ],
    'news' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/6/61/Central_Museum_of_the_Great_Patriotic_War%2C_Moscow%2C_Russia%2C_2016_32.jpg/800px-Central_Museum_of_the_Great_Patriotic_War%2C_Moscow%2C_Russia%2C_2016_32.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/Agreement_between_FISU_and_IFMA.jpg/800px-Agreement_between_FISU_and_IFMA.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/UN_General_Assembly.jpg/800px-UN_General_Assembly.jpg',
    ],
    'church' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/Russian_Orthodox_Church_in_Tallinn.jpg/800px-Russian_Orthodox_Church_in_Tallinn.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Blessed_Virgin_of_Vladmir_Russian_Orthodox_Church%2C_Rocklea%2C_2007.jpg/800px-Blessed_Virgin_of_Vladmir_Russian_Orthodox_Church%2C_Rocklea%2C_2007.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Aelia_Capitolina_%2815087534494%29.jpg/800px-Aelia_Capitolina_%2815087534494%29.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Bryansk%2C_Bryansk_Oblast%2C_Russia_-_panoramio.jpg/800px-Bryansk%2C_Bryansk_Oblast%2C_Russia_-_panoramio.jpg',
    ],
    'publishing' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/4/42/Books_helf.jpg/800px-Books_helf.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Books_and_cup.jpg/800px-Books_and_cup.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/9/92/Book_pen_cup.jpg/800px-Book_pen_cup.jpg',
    ],
    'orphan' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/Children_playing_in_Kiev.JPG/800px-Children_playing_in_Kiev.JPG',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0c/Child_reading_at_Brookline_Booksmith.jpg/800px-Child_reading_at_Brookline_Booksmith.jpg',
    ],
    'veteran' => [
        'https://upload.wikimedia.org/wikipedia/commons/thumb/6/61/Central_Museum_of_the_Great_Patriotic_War%2C_Moscow%2C_Russia%2C_2016_32.jpg/800px-Central_Museum_of_the_Great_Patriotic_War%2C_Moscow%2C_Russia%2C_2016_32.jpg',
        'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Memorial_Moscow.jpg/800px-Memorial_Moscow.jpg',
    ],
];

$items = [];
foreach (\App\Models\Art::orderBy('id')->get(['id', 'title', 'cover']) as $m) {
    if ($m->cover && pathToFull($m->cover)) $items[] = ['type' => 'art', 'title' => $m->title ?? '', 'path' => $m->cover];
}
foreach (\App\Models\News::orderBy('id')->get(['id', 'title', 'cover']) as $m) {
    if ($m->cover && pathToFull($m->cover)) $items[] = ['type' => 'news', 'title' => $m->title ?? '', 'path' => $m->cover];
}
foreach (\App\Models\Church::orderBy('id')->get(['id', 'title', 'cover']) as $m) {
    if ($m->cover && pathToFull($m->cover)) $items[] = ['type' => 'church', 'title' => $m->title ?? '', 'path' => $m->cover];
}
foreach (\App\Models\Publishing::orderBy('id')->get(['id', 'title', 'cover']) as $m) {
    if ($m->cover && pathToFull($m->cover)) $items[] = ['type' => 'publishing', 'title' => $m->title ?? '', 'path' => $m->cover];
}
foreach (\App\Models\Orphan::orderBy('id')->get(['id', 'cover']) as $m) {
    if ($m->cover && pathToFull($m->cover)) $items[] = ['type' => 'orphan', 'title' => 'дети благотворительность', 'path' => $m->cover];
}
foreach (\App\Models\Veteran::orderBy('id')->get(['id', 'cover']) as $m) {
    if ($m->cover && pathToFull($m->cover)) $items[] = ['type' => 'veteran', 'title' => 'ветераны память', 'path' => $m->cover];
}

$listFile = $base . '/scripts/thematic-download-list.txt';
$fh = fopen($listFile, 'w');
if (!$fh) {
    echo "Не удалось создать " . $listFile . "\n";
    exit(1);
}

$usedUrls = [];
$ok = 0;
foreach ($items as $item) {
    $full = pathToFull($item['path']);
    $rel = str_replace($base . '/', '', $full);
    if (strpos($rel, '|') !== false) continue;

    $url = findCommonsImageByTitle($item['title'], $usedUrls);
    if (!$url && isset($searchFallbackByType[$item['type']])) {
        $url = findCommonsImageByTitle($searchFallbackByType[$item['type']], $usedUrls);
    }
    if (!$url && isset($fallbackUrlsByType[$item['type']])) {
        $usedSet = array_flip($usedUrls);
        foreach ($fallbackUrlsByType[$item['type']] as $fb) {
            if (!isset($usedSet[$fb])) {
                $url = $fb;
                break;
            }
        }
    }
    if (!$url) {
        $usedSet = array_flip($usedUrls);
        foreach ($fallbackUrlsByType as $type => $list) {
            foreach ($list as $fb) {
                if (!isset($usedSet[$fb])) {
                    $url = $fb;
                    break 2;
                }
            }
        }
    }
    if (!$url) {
        $url = $fallbackUrlsByType['news'][0];
    }

    $usedUrls[] = $url;
    fwrite($fh, $rel . "|" . $url . "\n");
    echo "Подобрано: " . mb_substr($item['title'] ?: $item['path'], 0, 55) . "\n";
    $ok++;
}
fclose($fh);
echo "\nСписок записан в scripts/thematic-download-list.txt ($ok записей).\n";
echo "Загрузите картинки: bash scripts/download-thematic-from-list.sh\n";
