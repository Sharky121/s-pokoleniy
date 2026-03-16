<?php
/**
 * Находит все пути к изображениям в БД, проверяет наличие файлов,
 * для недостающих скачивает примерное изображение из интернета.
 * Запуск: php scripts/fill-missing-images.php
 */
$base = dirname(__DIR__);
require $base . '/vendor/autoload.php';
$app = require_once $base . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$publicPath = $base . '/public';
$storagePath = $base . '/storage/app/public';

function pathToFull(string $path, string $publicPath, string $storagePath): ?string {
    if (empty($path)) return null;
    if (strpos($path, '/storage/') === 0) {
        return rtrim($storagePath, '/') . '/' . ltrim(substr($path, 9), '/');
    }
    if (strpos($path, '/') === 0) {
        return $publicPath . $path;
    }
    return $publicPath . '/' . $path;
}

function collectPaths(string $publicPath, string $storagePath): array {
    $entries = [];
    $add = function (string $path, string $label) use ($publicPath, $storagePath, &$entries) {
        if (empty($path)) return;
        $full = pathToFull($path, $publicPath, $storagePath);
        if ($full) $entries[] = ['path' => $path, 'full' => $full, 'label' => $label];
    };

    \App\Models\Art::orderBy('id')->get(['id', 'title', 'cover'])->each(function ($m) use ($add) {
        $add($m->cover, "art:{$m->id} " . mb_substr($m->title ?? '', 0, 30));
    });
    \App\Models\ArtPhoto::orderBy('id')->get(['id', 'path'])->each(function ($m) use ($add) {
        $add($m->path, "art_photo:{$m->id}");
    });
    \App\Models\Church::orderBy('id')->get(['id', 'title', 'cover'])->each(function ($m) use ($add) {
        $add($m->cover, "church:{$m->id} " . mb_substr($m->title ?? '', 0, 30));
    });
    \App\Models\ChurchPhoto::orderBy('id')->get(['id', 'path'])->each(function ($m) use ($add) {
        $add($m->path, "church_photo:{$m->id}");
    });
    \App\Models\News::orderBy('id')->get(['id', 'title', 'cover'])->each(function ($m) use ($add) {
        $add($m->cover, "news:{$m->id} " . mb_substr($m->title ?? '', 0, 30));
    });
    \App\Models\NewsPhoto::orderBy('id')->get(['id', 'path'])->each(function ($m) use ($add) {
        $add($m->path, "news_photo:{$m->id}");
    });
    \App\Models\Orphan::orderBy('id')->get(['id', 'cover'])->each(function ($m) use ($add) {
        $add($m->cover, "orphan:{$m->id}");
    });
    \App\Models\OrphanPhoto::orderBy('id')->get(['id', 'path'])->each(function ($m) use ($add) {
        $add($m->path, "orphan_photo:{$m->id}");
    });
    \App\Models\Partner::orderBy('id')->get(['id', 'cover'])->each(function ($m) use ($add) {
        $add($m->cover, "partner:{$m->id}");
    });
    \App\Models\Publishing::orderBy('id')->get(['id', 'title', 'cover'])->each(function ($m) use ($add) {
        $add($m->cover, "publishing:{$m->id} " . mb_substr($m->title ?? '', 0, 30));
    });
    \App\Models\PublishingPhoto::orderBy('id')->get(['id', 'path'])->each(function ($m) use ($add) {
        $add($m->path, "publishing_photo:{$m->id}");
    });
    \App\Models\Veteran::orderBy('id')->get(['id', 'cover'])->each(function ($m) use ($add) {
        $add($m->cover, "veteran:{$m->id}");
    });
    \App\Models\VeteranPhoto::orderBy('id')->get(['id', 'path'])->each(function ($m) use ($add) {
        $add($m->path, "veteran_photo:{$m->id}");
    });
    \App\Models\PageGalleryPhoto::orderBy('id')->get(['id', 'path'])->each(function ($m) use ($add) {
        $add($m->path, "page_gallery_photo:{$m->id}");
    });

    return $entries;
}

// Плейсхолдеры по типу контента (Wikimedia Commons, CC)
$placeholders = [
    'default' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/61/Central_Museum_of_the_Great_Patriotic_War%2C_Moscow%2C_Russia%2C_2016_32.jpg/800px-Central_Museum_of_the_Great_Patriotic_War%2C_Moscow%2C_Russia%2C_2016_32.jpg',
    'art' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Teatro_alla_Scala_Milan_-_panoramio.jpg/800px-Teatro_alla_Scala_Milan_-_panoramio.jpg',
    'church' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/Russian_Orthodox_Church_in_Tallinn.jpg/800px-Russian_Orthodox_Church_in_Tallinn.jpg',
    'news' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/61/Central_Museum_of_the_Great_Patriotic_War%2C_Moscow%2C_Russia%2C_2016_32.jpg/800px-Central_Museum_of_the_Great_Patriotic_War%2C_Moscow%2C_Russia%2C_2016_32.jpg',
    'orphan' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/Children_playing_in_Kiev.JPG/800px-Children_playing_in_Kiev.JPG',
    'partner' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Placeholder.png/800px-Placeholder.png',
    'publishing' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/Books_flat_icon.svg/800px-Books_flat_icon.svg.png',
    'veteran' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/61/Central_Museum_of_the_Great_Patriotic_War%2C_Moscow%2C_Russia%2C_2016_32.jpg/800px-Central_Museum_of_the_Great_Patriotic_War%2C_Moscow%2C_Russia%2C_2016_32.jpg',
];

function pickPlaceholder(string $label, array $placeholders): string {
    if (strpos($label, 'art') === 0) return $placeholders['art'];
    if (strpos($label, 'church') === 0) return $placeholders['church'];
    if (strpos($label, 'news') === 0) return $placeholders['news'];
    if (strpos($label, 'orphan') === 0) return $placeholders['orphan'];
    if (strpos($label, 'partner') === 0) return $placeholders['partner'];
    if (strpos($label, 'publishing') === 0) return $placeholders['publishing'];
    if (strpos($label, 'veteran') === 0) return $placeholders['veteran'];
    return $placeholders['default'];
}

function downloadTo(string $url, string $fullPath): bool {
    $ctx = stream_context_create(['http' => ['timeout' => 30, 'follow_location' => true]]);
    $data = @file_get_contents($url, false, $ctx);
    if ($data === false || strlen($data) < 100) return false;
    $dir = dirname($fullPath);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return file_put_contents($fullPath, $data) !== false;
}

$all = collectPaths($publicPath, $storagePath);
$missing = array_filter($all, function ($e) { return !file_exists($e['full']); });

$listOnly = in_array('--list', $GLOBALS['argv'] ?? [], true);
$listAll = in_array('--list-all', $GLOBALS['argv'] ?? [], true);
if ($listOnly || $listAll) {
    $entries = $listAll ? $all : $missing;
    foreach ($entries as $e) {
        $url = pickPlaceholder($e['label'], $placeholders);
        $rel = str_replace($base . '/', '', $e['full']);
        if (strpos($rel, '|') !== false) continue;
        echo $rel . "|" . $url . "\n";
    }
    if ($listOnly) echo "Всего отсутствует: " . count($missing) . "\n";
    if ($listAll) echo "Всего путей: " . count($entries) . "\n";
    exit(0);
}

echo "Всего путей: " . count($all) . ", отсутствует файлов: " . count($missing) . "\n";
if (empty($missing)) {
    echo "Недостающих картинок нет.\n";
    exit(0);
}

foreach ($missing as $e) {
    $url = pickPlaceholder($e['label'], $placeholders);
    $ext = pathinfo(parse_url($e['path'], PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
    $fullPath = $e['full'];
    if ($ext === 'svg' || $ext === 'png') {
        $url = $ext === 'png' ? 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Placeholder.png/800px-Placeholder.png' : $url;
    }
    echo "Загружаю: {$e['label']} -> " . basename($fullPath) . " ... ";
    if (downloadTo($url, $fullPath)) {
        echo "OK\n";
    } else {
        echo "Ошибка\n";
    }
}
echo "Готово.\n";
