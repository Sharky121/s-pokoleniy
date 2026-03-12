<?php

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