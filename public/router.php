<?php

/**
 * Роутер для встроенного PHP-сервера (php artisan serve и аналогов).
 * Запросы к /storage/* отдаём через Laravel, чтобы выставлялся правильный Content-Type (CSS, JS и т.д.).
 * Остальные запросы — как обычно (статичные файлы или index.php).
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if ($uri !== '/' && preg_match('#^/storage/#', $uri)) {
    require __DIR__ . '/index.php';
    return true;
}
return false;
