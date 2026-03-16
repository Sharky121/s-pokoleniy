<?php
/**
 * Раздел «Издательская деятельность» (publishings): по title ищет тематическую картинку (книги, издание)
 * в Wikimedia Commons, сохраняет в public/images, приводит к 800×600, пишет путь в cover в БД.
 * Картинки не повторяются. Запуск: php scripts/publishings-fill-covers.php [--all]
 */
$base = dirname(__DIR__);
require $base . '/vendor/autoload.php';
$app = require_once $base . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$imagesDir = $base . '/public/images';
$processAll = in_array('--all', array_slice($argv, 1), true);

function isThematicPublishingImage(string $pageTitle): bool
{
    $lower = mb_strtolower($pageTitle);
    $thematic = [
        'book', 'books', 'книг', 'publishing', 'издани', 'literature', 'литератур', 'author', 'писатель',
        'library', 'библиотек', 'reading', 'чтени', 'publication', 'печат', 'presentation', 'презентаци',
        'manuscript', 'рукопис', 'poetry', 'поэзи', 'novel', 'роман', 'cover book', 'обложка',
    ];
    foreach ($thematic as $keyword) {
        if (mb_strpos($lower, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

function findCommonsImageByTitle(string $title, array &$usedUrls): ?string
{
    $query = trim(preg_replace('/\s+/', ' ', $title));
    $searchQueries = [];
    if ($query !== '') {
        $searchQueries[] = 'книга издание ' . $query;
        $searchQueries[] = 'book publishing ' . $query;
        $searchQueries[] = $query;
    }
    $searchQueries[] = 'book presentation';
    $searchQueries[] = 'книга издание литература';

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
            if (!isThematicPublishingImage($pageTitle)) {
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

$publishingsFallbackUrls = [
    'https://upload.wikimedia.org/wikipedia/commons/thumb/4/42/Books_helf.jpg/800px-Books_helf.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Books_and_cup.jpg/800px-Books_and_cup.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/9/92/Book_pen_cup.jpg/800px-Book_pen_cup.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Books_%28remixed%29.jpg/800px-Books_%28remixed%29.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Open_book_nae_02.jpg/800px-Open_book_nae_02.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1d/Bookshelf.jpg/800px-Bookshelf.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Assorted_books.jpg/800px-Assorted_books.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5a/Books_IMG_1965.jpg/800px-Books_IMG_1965.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Library_of_Congress_Reading_Room.jpg/800px-Library_of_Congress_Reading_Room.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/9/94/Author_at_work.jpg/800px-Author_at_work.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Book_fair.jpg/800px-Book_fair.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/Reading_room.jpg/800px-Reading_room.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/7/72/Old_books.jpg/800px-Old_books.jpg',
    'https://upload.wikimedia.org/wikipedia/commons/thumb/4/48/Book_stack.jpg/800px-Book_stack.jpg',
];

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
        'http' => ['timeout' => 25, 'user_agent' => $ua, 'header' => "Referer: $referer"],
    ]);
    $data = @file_get_contents($url, false, $ctx);
    if ($data === false || strlen($data) < 5000) {
        return false;
    }
    return file_put_contents($filepath, $data) !== false;
}

function coverFilename(int $id): string
{
    return "publishing-{$id}.jpg";
}

const COVER_WIDTH = 800;
const COVER_HEIGHT = 600;

function resizeToUniformSize(string $filepath): bool
{
    try {
        $driver = extension_loaded('imagick') ? 'imagick' : 'gd';
        $manager = new \Intervention\Image\ImageManager(['driver' => $driver]);
        $img = $manager->make($filepath);
        $img->fit(COVER_WIDTH, COVER_HEIGHT);
        $img->encode('jpg', 88);
        $img->save($filepath);
        return true;
    } catch (\Throwable $e) {
        return false;
    }
}

$items = \App\Models\Publishing::orderBy('id')->get(['id', 'title', 'cover']);
if (!$processAll) {
    $items = $items->filter(function ($c) {
        return empty($c->cover) || trim($c->cover) === '';
    });
}

if ($items->isEmpty()) {
    echo "Нет записей для обработки (используйте --all для обработки всех).\n";
    exit(0);
}

if (!is_dir($imagesDir)) {
    mkdir($imagesDir, 0755, true);
}

$usedUrls = [];
$updated = [];
$failed = [];

foreach ($items as $item) {
    $title = $item->title ?? '';
    $placehold = null;
    $url = findCommonsImageByTitle($title, $usedUrls);
    if (!$url) {
        $usedSet = array_flip($usedUrls);
        foreach ($publishingsFallbackUrls as $fb) {
            if (!isset($usedSet[$fb])) {
                $url = $fb;
                break;
            }
        }
    }
    if (!$url) {
        $failed[] = $item->id . ': ' . mb_substr($title, 0, 50);
        continue;
    }

    $filename = coverFilename($item->id);
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
        foreach ($publishingsFallbackUrls as $fb) {
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
            echo "OK (placeholder): id={$item->id} " . mb_substr($title, 0, 45) . " → /images/{$filename}\n";
        }
    }

    if (!$downloaded) {
        $failed[] = $item->id . ': ' . mb_substr($title, 0, 50);
        continue;
    }

    resizeToUniformSize($filepath);

    $usedUrls[] = $usedUrl;
    $coverPath = '/images/' . $filename;
    $item->cover = $coverPath;
    $item->save();
    $updated[] = $item->id . ' — ' . $coverPath;
    if ($placehold === null) {
        echo "OK: id={$item->id} " . mb_substr($title, 0, 45) . " → {$coverPath}\n";
    }
}

if (!empty($failed)) {
    echo "\nНе удалось: " . implode("\n  ", $failed) . "\n";
}

echo "\nОбновлено записей: " . count($updated) . "\n";

if (count($updated) > 0) {
    echo "Добавьте в git:\n  git add public/images/publishing-*.jpg scripts/publishings-fill-covers.php\n  git commit -m \"Обложки раздел Издательская деятельность\"\n";
}
