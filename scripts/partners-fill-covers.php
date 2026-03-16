<?php
/**
 * Страница «Партнёры»: для каждого партнёра переходит по полю site, забирает именно логотип со страницы
 * (приоритет: img с классом logo/brand, apple-touch-icon, icon, og:image, favicon.ico), сохраняет в public/images,
 * приводит к 400×400, пишет в cover. Запуск: php scripts/partners-fill-covers.php [--all]
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
 * Из HTML страницы извлекает URL именно логотипа (приоритет: img.logo в шапке, затем apple-touch-icon, icon, og:image, favicon).
 */
function extractLogoUrlFromHtml(string $html, string $pageUrl): ?string
{
    $decode = function ($s) {
        return trim(html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    };
    $resolve = function ($path) use ($pageUrl) {
        return resolveAbsoluteUrl($pageUrl, $path);
    };

    $header = mb_substr($html, 0, 150000);

    // 1. Картинка с логотипом в разметке (class/id: logo, brand, header-logo, site-logo, логотип и т.д.)
    $logoImgPattern = '#<img[^>]+(?:class|id)=["\']([^"\']*?(?:logo|brand|header-logo|site-logo|логотип|logotype)[^"\']*)["\'][^>]+(?:src|data-src)=["\']([^"\']+)["\']#i';
    if (preg_match($logoImgPattern, $header, $m)) {
        $src = $decode($m[2]);
        if (stripos($src, '.svg') === false) {
            return $resolve($src);
        }
    }
    $logoImgPattern2 = '#<img[^>]+(?:src|data-src)=["\']([^"\']+)["\'][^>]+(?:class|id)=["\']([^"\']*?(?:logo|brand|header-logo|site-logo|логотип)[^"\']*)["\']#i';
    if (preg_match($logoImgPattern2, $header, $m)) {
        $src = $decode($m[1]);
        if (stripos($src, '.svg') === false) {
            return $resolve($src);
        }
    }

    // 2. Apple-touch-icon (обычно логотип 180x180)
    if (preg_match_all('#<link[^>]+href=["\']([^"\']+)["\'][^>]+rel=["\'](?:apple-touch-icon)[^"\']*["\'][^>]*>#i', $header, $m)) {
        foreach ($m[1] as $href) {
            $href = $decode($href);
            if (stripos($href, '.svg') === false) {
                return $resolve($href);
            }
        }
    }

    // 3. link rel="icon" с размером (предпочтительно крупнее)
    if (preg_match_all('#<link[^>]+rel=["\'](?:icon)[^"\']*["\'][^>]+href=["\']([^"\']+)["\'][^>]*>#i', $header, $m)) {
        foreach ($m[1] as $href) {
            $href = $decode($href);
            if (stripos($href, '.svg') === false) {
                return $resolve($href);
            }
        }
    }
    if (preg_match_all('#<link[^>]+href=["\']([^"\']+)["\'][^>]+rel=["\'](?:icon)[^"\']*["\'][^>]*>#i', $header, $m)) {
        foreach ($m[1] as $href) {
            $href = $decode($href);
            if (stripos($href, '.svg') === false) {
                return $resolve($href);
            }
        }
    }

    // 4. og:image (часто превью страницы, но иногда логотип)
    if (preg_match('#<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']#i', $header, $m)) {
        return $resolve($decode($m[1]));
    }
    if (preg_match('#<meta[^>]+content=["\']([^"\']+)["\'][^>]+property=["\']og:image["\']#i', $header, $m)) {
        return $resolve($decode($m[1]));
    }

    // 5. favicon.ico в последнюю очередь
    $parts = parse_url($pageUrl);
    $base = ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? '');
    return $base . '/favicon.ico';
}

/** Для МПДА используем официальный логотип Shape.svg (внутри встроен PNG). */
const MDPA_LOGO_URL = 'https://mpda.ru/wp-content/themes/mda/img/Shape.svg';
/** Для РГМ — логотип из шапки сайта (чистый SVG). */
const RHM_LOGO_URL = 'https://rhm.agency/wp-content/themes/Rhm/assets/img/logoHeader.svg';

/**
 * Скачивает SVG по URL и извлекает встроенное растровое изображение (data:image/png;base64,...).
 * Сохраняет декодированные байты в $filepath. Возвращает true при успехе.
 */
function downloadSvgWithEmbeddedImage(string $url, string $filepath, string $userAgent): bool
{
    $svg = fetchUrl($url, $userAgent, 25);
    if ($svg === null || $svg === '') {
        return false;
    }
    if (preg_match('#data:image/(png|jpe?g);base64,([A-Za-z0-9+/=]+)#', $svg, $m)) {
        $raw = base64_decode($m[2], true);
        if ($raw !== false && strlen($raw) >= 100) {
            return file_put_contents($filepath, $raw) !== false;
        }
    }
    return false;
}

/**
 * Скачивает SVG (без встроенного растра) и конвертирует в растровый файл.
 * Сначала пробует Imagick (если собран с librsvg), затем rsvg-convert или convert в системе.
 */
function downloadSvgAndConvertToRaster(string $url, string $filepath, string $userAgent): bool
{
    $svg = fetchUrl($url, $userAgent, 25);
    if ($svg === null || $svg === '') {
        return false;
    }
    $dir = dirname($filepath);
    $tmpSvg = $dir . '/_tmp_' . uniqid() . '.svg';
    $tmpPng = $dir . '/_tmp_' . uniqid() . '.png';
    if (file_put_contents($tmpSvg, $svg) === false) {
        return false;
    }
    $ok = false;

    if (extension_loaded('imagick')) {
        try {
            $im = new \Imagick();
            $im->setResolution(200, 200);
            $im->readImage($tmpSvg);
            $im->setImageFormat('png');
            $im->writeImage($tmpPng);
            $im->clear();
            $im->destroy();
            if (is_file($tmpPng) && filesize($tmpPng) >= 100) {
                $ok = rename($tmpPng, $filepath);
            }
        } catch (\Throwable $e) {
            // Imagick без поддержки SVG — пробуем консоль
        }
    }
    if (!$ok && is_executable_available('rsvg-convert')) {
        $out = [];
        @exec('rsvg-convert -o ' . escapeshellarg($filepath) . ' ' . escapeshellarg($tmpSvg) . ' 2>&1', $out);
        $ok = is_file($filepath) && filesize($filepath) >= 100;
    }
    if (!$ok && is_executable_available('convert')) {
        $out = [];
        @exec('convert ' . escapeshellarg($tmpSvg) . ' ' . escapeshellarg($filepath) . ' 2>&1', $out);
        $ok = is_file($filepath) && filesize($filepath) >= 100;
    }
    @unlink($tmpSvg);
    @unlink($tmpPng);
    return $ok;
}

function is_executable_available(string $cmd): bool
{
    $path = trim(explode("\n", (string) @shell_exec('which ' . escapeshellarg($cmd) . ' 2>/dev/null'))[0]);
    return $path !== '' && $path !== '0' && @is_executable($path);
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

    // Для МПДА и РГМ используем фиксированные URL логотипов
    if (strpos($site, 'mpda.ru') !== false) {
        $logoUrl = MDPA_LOGO_URL;
    }
    if (strpos($site, 'rhm.agency') !== false) {
        $logoUrl = RHM_LOGO_URL;
    }

    $downloaded = false;
    if (stripos($logoUrl, '.svg') !== false) {
        $downloaded = downloadSvgWithEmbeddedImage($logoUrl, $filepath, $ua);
        if (!$downloaded) {
            $downloaded = downloadSvgAndConvertToRaster($logoUrl, $filepath, $ua);
        }
        if (!$downloaded) {
            $failed[] = $item->id . ': ' . $item->title . ' (SVG: нет встроенного растра и конвертация Imagick недоступна)';
            continue;
        }
    }
    if (!$downloaded && !downloadToFile($logoUrl, $filepath, $ua)) {
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
