<?php
/**
 * Страница «Партнёры»: для каждого партнёра переходит по полю site, забирает логотип со страницы
 * (og:image, favicon, или img с классом/id «logo»), сохраняет в public/images, приводит к 400×400, пишет в cover.
 * Запуск: php scripts/partners-fill-covers.php [--all]
 */
$base = dirname(__DIR__);
require $base . '/vendor/autoload.php';
$app = require_once $base . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$imagesDir = $base . '/public/images';
$processAll = in_array('--all', array_slice($argv, 1), true);

$ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

function fetchUrl(string $url, string $userAgent, int $timeout = 15): ?string
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($code >= 200 && $code < 400 && $body !== false) ? $body : null;
    }
    $ctx = stream_context_create([
        'http' => ['timeout' => $timeout, 'user_agent' => $userAgent],
    ]);
    $body = @file_get_contents($url, false, $ctx);
    return $body !== false ? $body : null;
}

function resolveAbsoluteUrl(string $baseUrl, string $path): string
{
    $path = trim($path);
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    $parts = parse_url($baseUrl);
    $scheme = $parts['scheme'] ?? 'https';
    $host = $parts['host'] ?? '';
    $basePath = isset($parts['path']) ? preg_replace('#/[^/]*$#', '/', $parts['path']) : '/';
    if (strpos($path, '//') === 0) {
        return $scheme . ':' . $path;
    }
    if (strpos($path, '/') === 0) {
        return $scheme . '://' . $host . $path;
    }
    return $scheme . '://' . $host . $basePath . ltrim($path, '/');
}

/**
 * Из HTML страницы извлекает URL логотипа: og:image, link icon/apple-touch-icon, img.logo, затем favicon.ico.
 */
function extractLogoUrlFromHtml(string $html, string $pageUrl): ?string
{
    $url = null;
    if (preg_match('#<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']#i', $html, $m)) {
        $url = trim(html_entity_decode($m[1]));
    }
    if (!$url && preg_match('#<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\']#i', $html, $m)) {
        $url = trim(html_entity_decode($m[1]));
    }
    if ($url) {
        return resolveAbsoluteUrl($pageUrl, $url);
    }
    $icons = [];
    if (preg_match_all('#<link[^>]+(?:rel=["\'](?:apple-touch-icon|icon)[^"\']*["\']|href=["\']([^"\']+)["\'][^>]+rel=["\'](?:apple-touch-icon|icon))[^>]*(?:href=["\']([^"\']+)["\']|rel=["\'](?:apple-touch-icon|icon)[^"\']*["\'])[^>]*>#i', $html, $m)) {
        foreach ($m[0] as $i => $tag) {
            if (preg_match('#href=["\']([^"\']+)["\']#i', $tag, $href)) {
                $icons[] = trim(html_entity_decode($href[1]));
            }
        }
    }
    if (preg_match_all('#<link[^>]+href=["\']([^"\']+)["\'][^>]+rel=["\'](?:apple-touch-icon|icon)[^"\']*["\']#i', $html, $m)) {
        foreach ($m[1] as $href) {
            $icons[] = trim(html_entity_decode($href));
        }
    }
    foreach (array_unique($icons) as $icon) {
        if (stripos($icon, '.svg') === false) {
            return resolveAbsoluteUrl($pageUrl, $icon);
        }
    }
    if (preg_match('#<img[^>]+(?:class|id)=["\'][^"\']*logo[^"\']*["\'][^>]+src=["\']([^"\']+)["\']#i', $html, $m)) {
        return resolveAbsoluteUrl($pageUrl, trim(html_entity_decode($m[1])));
    }
    if (preg_match('#<img[^>]+src=["\']([^"\']+)["\'][^>]+(?:class|id)=["\'][^"\']*logo[^"\']*["\']#i', $html, $m)) {
        return resolveAbsoluteUrl($pageUrl, trim(html_entity_decode($m[1])));
    }
    $parts = parse_url($pageUrl);
    $base = ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '');
    return $base . '/favicon.ico';
}

function downloadToFile(string $url, string $filepath, string $userAgent): bool
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $data = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);
        if ($err === 0 && $data !== false && strlen($data) >= 100) {
            return file_put_contents($filepath, $data) !== false;
        }
    }
    $ctx = stream_context_create(['http' => ['timeout' => 25, 'user_agent' => $userAgent]]);
    $data = @file_get_contents($url, false, $ctx);
    if ($data === false || strlen($data) < 100) {
        return false;
    }
    return file_put_contents($filepath, $data) !== false;
}

function coverFilename(int $id): string
{
    return "partner-{$id}.jpg";
}

const LOGO_SIZE = 400;

function resizeToUniformSize(string $filepath): bool
{
    try {
        $driver = extension_loaded('imagick') ? 'imagick' : 'gd';
        $manager = new \Intervention\Image\ImageManager(['driver' => $driver]);
        $img = $manager->make($filepath);
        $img->fit(LOGO_SIZE, LOGO_SIZE);
        $img->encode('jpg', 88);
        $img->save($filepath);
        return true;
    } catch (\Throwable $e) {
        return false;
    }
}

$items = \App\Models\Partner::orderBy('id')->get(['id', 'title', 'site', 'cover']);
if (!$processAll) {
    $items = $items->filter(function ($c) {
        return empty($c->cover) || trim($c->cover) === '';
    });
}

$items = $items->filter(function ($c) {
    return !empty(trim($c->site ?? ''));
});

if ($items->isEmpty()) {
    echo "Нет партнёров с заполненным site для обработки (или используйте --all).\n";
    exit(0);
}

if (!is_dir($imagesDir)) {
    mkdir($imagesDir, 0755, true);
}

$updated = [];
$failed = [];

foreach ($items as $item) {
    $site = trim($item->site);
    if (strpos($site, 'http') !== 0) {
        $site = 'https://' . $site;
    }
    $filename = coverFilename($item->id);
    $filepath = $imagesDir . '/' . $filename;

    $html = fetchUrl($site, $ua);
    if (!$html && strpos($site, 'https://') === 0) {
        $siteHttp = 'http://' . substr($site, 8);
        $html = fetchUrl($siteHttp, $ua);
        if ($html) {
            $site = $siteHttp;
        }
    }
    if (!$html) {
        $failed[] = $item->id . ': ' . $item->title . ' (не удалось загрузить страницу)';
        continue;
    }

    $logoUrl = extractLogoUrlFromHtml($html, $site);
    if (!$logoUrl) {
        $failed[] = $item->id . ': ' . $item->title . ' (логотип на странице не найден)';
        continue;
    }

    if (stripos($logoUrl, '.svg') !== false) {
        $failed[] = $item->id . ': ' . $item->title . ' (SVG логотип — конвертация не реализована)';
        continue;
    }

    if (!downloadToFile($logoUrl, $filepath, $ua)) {
        $failed[] = $item->id . ': ' . $item->title . ' (ошибка загрузки изображения)';
        continue;
    }

    resizeToUniformSize($filepath);

    $coverPath = '/images/' . $filename;
    $item->cover = $coverPath;
    $item->save();
    $updated[] = $item->id . ' — ' . $coverPath;
    echo "OK: id={$item->id} " . mb_substr($item->title, 0, 45) . " ← {$site}\n";
}

if (!empty($failed)) {
    echo "\nНе удалось:\n  " . implode("\n  ", $failed) . "\n";
}

echo "\nОбновлено записей: " . count($updated) . "\n";

if (count($updated) > 0) {
    echo "Добавьте в git:\n  git add public/images/partner-*.jpg scripts/partners-fill-covers.php\n  git commit -m \"Логотипы партнёров с сайтов\"\n";
}
