<?php
/**
 * Раздел «Поддержка РПЦ» (churches): по title ищет тематическую картинку (храм/церковь) в Wikimedia Commons,
 * сохраняет в public/images, пишет путь в cover в БД. Картинки не повторяются.
 * Принимаются только изображения с тематическими подписями (храм, church, orthodox и т.д.).
 * Запуск: php scripts/churches-fill-covers.php [--all]
 *   --all — обработать все записи (по умолчанию только с пустым cover)
 * Для надёжной загрузки с Commons лучше запускать на хосте (не в Docker): php scripts/churches-fill-covers.php --all
 */
$base = dirname(__DIR__);
require $base . '/vendor/autoload.php';
$app = require_once $base . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$imagesDir = $base . '/public/images';
$processAll = in_array('--all', array_slice($argv, 1), true);

/** Проверка, что имя файла/страницы похоже на тематику храма/церкви (православие, храм, купол и т.д.). */
function isThematicChurchImage(string $pageTitle): bool
{
    $lower = mb_strtolower($pageTitle);
    $thematic = [
        'church', 'храм', 'church', 'cathedral', 'собор', 'orthodox', 'православн', 'temple', 'часовня',
        'chapel', 'купол', 'dome', 'колокольн', 'bell tower', 'икона', 'icon', 'монастыр', 'monastery',
        'церковь', 'kirche', 'eglise', 'iglesia', 'chiesa', 'cerkiew', 'church of ', 'храма ', 'храме ',
    ];
    foreach ($thematic as $keyword) {
        if (mb_strpos($lower, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Поиск тематического изображения (храм/церковь) в Wikimedia Commons.
 * Сначала ищет по запросу с добавлением "православный храм", затем только по запросу.
 * Принимает только картинки, проходящие isThematicChurchImage. Не повторяет URL из $usedUrls.
 */
function findCommonsImageByTitle(string $title, array &$usedUrls): ?string
{
    $query = trim(preg_replace('/\s+/', ' ', $title));
    $searchQueries = [];
    if ($query !== '') {
        $searchQueries[] = 'православный храм ' . $query;
        $searchQueries[] = 'храм ' . $query;
        $searchQueries[] = $query;
    }
    $searchQueries[] = 'Russian Orthodox church';
    $searchQueries[] = 'православный храм Россия';

    $ctx = stream_context_create([
        'http' => ['timeout' => 15, 'user_agent' => 'Mozilla/5.0 (compatible; SvyazPokolenij/1.0; +https://s-pokoleniy.ru)'],
    ]);
    $usedSet = array_flip($usedUrls);

    foreach ($searchQueries as $gsrsearch) {
        $apiUrl = 'https://commons.wikimedia.org/w/api.php?' . http_build_query([
            'action' => 'query',
            'generator' => 'search',
            'gsrnamespace' => 6,
            'gsrsearch' => $gsrsearch,
            'gsrlimit' => 40,
            'prop' => 'imageinfo',
            'iiprop' => 'url|mime',
            'iiurlwidth' => 800,
            'format' => 'json',
        ]);
        $json = @file_get_contents($apiUrl, false, $ctx);
        if ($json === false) {
            continue;
        }
        $data = json_decode($json, true);
        if (empty($data['query']['pages'])) {
            continue;
        }
        foreach ($data['query']['pages'] as $page) {
            $pageTitle = $page['title'] ?? '';
            if (!isThematicChurchImage($pageTitle)) {
                continue;
            }
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
    }
    return null;
}

/** Только тематические обложки: православные храмы/церкви (fallback при отсутствии результата поиска). */
$churchFallbackUrls = [
    'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/Russian_Orthodox_Church_in_Tallinn.jpg/800px-Russian_Orthodox_Church_in_Tallinn.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/Church_of_St._Nicholas_in_Khamovniki_01.jpg/800px-Church_of_St._Nicholas_in_Khamovniki_01.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/Saint_Basil%27s_Cathedral_at_sunset.jpg/800px-Saint_Basil%27s_Cathedral_at_sunset.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Blessed_Virgin_of_Vladmir_Russian_Orthodox_Church%2C_Rocklea%2C_2007.jpg/800px-Blessed_Virgin_of_Vladmir_Russian_Orthodox_Church%2C_Rocklea%2C_2007.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Bryansk%2C_Bryansk_Oblast%2C_Russia_-_panoramio.jpg/800px-Bryansk%2C_Bryansk_Oblast%2C_Russia_-_panoramio.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/Church_of_Christ_the_Saviour_Moscow_2.jpg/800px-Church_of_Christ_the_Saviour_Moscow_2.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7d/Novodevichy_Convent_in_Moscow-2.jpg/800px-Novodevichy_Convent_in_Moscow-2.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/8/88/Trinity_Lavra_of_St._Sergius_-_Trinity_Cathedral.jpg/800px-Trinity_Lavra_of_St._Sergius_-_Trinity_Cathedral.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Dormition_Cathedral_in_Vladimir_2.jpg/800px-Dormition_Cathedral_in_Vladimir_2.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e8/Church_of_the_Intercession_on_the_Nerl.jpg/800px-Church_of_the_Intercession_on_the_Nerl.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/Kizhi_Church_of_the_Transfiguration.jpg/800px-Kizhi_Church_of_the_Transfiguration.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d7/Assumption_Cathedral_in_Smolensk.jpg/800px-Assumption_Cathedral_in_Smolensk.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Epiphany_Cathedral_in_Elokhovo.jpg/800px-Epiphany_Cathedral_in_Elokhovo.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Church_of_St._George%2C_Staraya_Ladoga.jpg/800px-Church_of_St._George%2C_Staraya_Ladoga.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Yaroslavl_Church_of_Elijah_the_Prophet_2.jpg/800px-Yaroslavl_Church_of_Elijah_the_Prophet_2.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3b/Church_of_the_Savior_on_Blood%2C_St._Petersburg.jpg/800px-Church_of_the_Savior_on_Blood%2C_St._Petersburg.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/St._Nicholas_Naval_Cathedral_St._Petersburg.jpg/800px-St._Nicholas_Naval_Cathedral_St._Petersburg.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Church_of_the_Resurrection%2C_Kostroma.jpg/800px-Church_of_the_Resurrection%2C_Kostroma.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/Orthodox_Church_in_Tula.jpg/800px-Orthodox_Church_in_Tula.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/0/09/Ipatiev_Monastery_Kostroma.jpg/800px-Ipatiev_Monastery_Kostroma.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Dmitrov_Cathedral_Vladimir.jpg/800px-Dmitrov_Cathedral_Vladimir.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Church_of_St._John_the_Baptist_Yaroslavl.jpg/800px-Church_of_St._John_the_Baptist_Yaroslavl.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/8/85/Church_of_Elijah_Prophet_Yaroslavl.jpg/800px-Church_of_Elijah_Prophet_Yaroslavl.jpg',
];

/** Скачать по URL в файл. Возвращает true при успехе. Пробует curl, затем file_get_contents. */
function downloadToFile(string $url, string $filepath): bool
{
    $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    $referer = 'https://commons.wikimedia.org/';
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_USERAGENT => $ua,
            CURLOPT_HTTPHEADER => ["Referer: $referer"],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $data = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);
        if ($err === 0 && $data !== false && strlen($data) >= 5000) {
            return file_put_contents($filepath, $data) !== false;
        }
    }
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 25,
            'user_agent' => $ua,
            'header' => "Referer: $referer",
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
    $placehold = null;
    $url = findCommonsImageByTitle($title, $usedUrls);
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

    $filename = coverFilename($church->id);
    $filepath = $imagesDir . '/' . $filename;
    $downloaded = false;
    $usedUrl = null;
    while ($url) {
        if (downloadToFile($url, $filepath)) {
            $downloaded = true;
            $usedUrl = $url;
            break;
        }
        $usedUrls[] = $url;
        $url = null;
        $usedSet = array_flip($usedUrls);
        foreach ($churchFallbackUrls as $fb) {
            if (!isset($usedSet[$fb])) {
                $url = $fb;
                break;
            }
        }
    }

    if (!$downloaded) {
        $placehold = 'https://placehold.co/800x600.jpg?text=' . rawurlencode(mb_substr($title, 0, 30));
        if (downloadToFile($placehold, $filepath)) {
            $downloaded = true;
            $usedUrl = $placehold;
            echo "OK (placeholder): id={$church->id} " . mb_substr($title, 0, 45) . " → /images/{$filename}\n";
        }
    }

    if (!$downloaded) {
        $failed[] = $church->id . ': ' . mb_substr($title, 0, 50);
        continue;
    }

    $usedUrls[] = $usedUrl;
    $coverPath = '/images/' . $filename;
    $church->cover = $coverPath;
    $church->save();
    $updated[] = $church->id . ' — ' . $coverPath;
    if ($placehold === null) {
        echo "OK: id={$church->id} " . mb_substr($title, 0, 45) . " → {$coverPath}\n";
    }
}

if (!empty($failed)) {
    echo "\nНе удалось: " . implode("\n  ", $failed) . "\n";
}

echo "\nОбновлено записей: " . count($updated) . "\n";

if (count($updated) > 0) {
    echo "Добавьте в git:\n  git add public/images/church-*.jpg\n  git commit -m \"Обложки раздел Поддержка РПЦ\"\n";
}
