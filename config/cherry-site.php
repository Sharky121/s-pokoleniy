<?php

return [
    // Редирект после авторизации
    'redirectTo' => 'cherry-site.admin.settings.index',

    // Файл с роутами
    'routes' => base_path('routes/cherry-site.php'),

    'sidenav' => [
        ['routes' => ['admin.news.*'], 'title' => 'Новости', 'route' => 'admin.news.index', 'icon' => 'mdi mdi-newspaper'],
        ['routes' => ['admin.art.*'], 'title' => 'Творческая мастерская', 'route' => 'admin.art.index', 'icon' => 'mdi mdi-palette'],
        ['routes' => ['admin.churches.*'], 'title' => 'Поддержка РПЦ', 'route' => 'admin.churches.index', 'icon' => 'mdi mdi-church'],
        ['routes' => ['admin.publishings.*'], 'title' => 'Издательская деятельность', 'route' => 'admin.publishings.index', 'icon' => 'mdi mdi-book-open'],
        ['routes' => ['admin.orphans.*'], 'title' => 'Помощь детям', 'route' => 'admin.orphans.index', 'icon' => 'mdi mdi-baby'],
        ['routes' => ['admin.veterans.*'], 'title' => 'Помощь ветеранам войн', 'route' => 'admin.veterans.index', 'icon' => 'mdi mdi-trophy-award'],
        ['routes' => ['admin.quotes.*'], 'title' => 'Цитаты', 'route' => 'admin.quotes.index', 'icon' => 'mdi mdi-format-quote'],
        ['routes' => ['admin.partners.*'], 'title' => 'Партнёры', 'route' => 'admin.partners.index', 'icon' => 'mdi mdi-thumb-up'],
        ['routes' => ['admin.pages.*'], 'title' => 'Страницы', 'route' => 'admin.pages.index', 'icon' => 'mdi mdi-language-html5'],
    ],
];