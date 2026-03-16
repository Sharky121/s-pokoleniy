<?php

// Раздача файлов из storage, если веб-сервер не отдаёт их по симлинку (php artisan serve, nginx без алиаса и т.п.)
Route::get('storage/{path}', function ($path) {
    $path = str_replace(['../', '..'], '', $path);
    $fullPath = storage_path('app/public/' . $path);
    if (!is_file($fullPath)) {
        abort(404);
    }
    $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    $mimeMap = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
        'ico' => 'image/x-icon',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
    ];
    $mime = $mimeMap[$ext] ?? \Illuminate\Support\Facades\File::mimeType($fullPath);
    $content = file_get_contents($fullPath);
    return response($content, 200, [
        'Content-Type' => $mime,
        'Content-Length' => (string) strlen($content),
    ]);
})->where('path', '.+')->name('storage.serve');

// Регистрируем отдельные страницы
if (\Schema::hasTable('pages')) {
    // Миграции выполнены - можем регистрировать роуты
    \App\Models\Page::active()->each(function ($page) {
        if (is_null($page->url)) {
            return;
        }

        Route::get($page->compileUrl(), [
            'as' => "pages.{$page->id}",
            'page' => $page,
            'uses' => 'Site\\PagesController@index',
        ]);
    });
}