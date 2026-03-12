<?php

use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

// НОВОСТИ
Breadcrumbs::register('admin.news.index', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->push('Новости', route('admin.news.index'));
});

Breadcrumbs::register('admin.news.create', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->parent('admin.news.index');
    $breadcrumbs->push('Новая новость', route('admin.news.create'));
});

Breadcrumbs::register('admin.news.edit', function (BreadcrumbsGenerator $breadcrumbs, $model) {
    $breadcrumbs->parent('admin.news.index');
    $breadcrumbs->push($model->title ?? '---', route('admin.news.edit', [$model]));
});
// ---------------------------

// ТВОРЧЕСКАЯ МАСТЕРСКАЯ
Breadcrumbs::register('admin.art.index', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->push('Творческая мастерская', route('admin.art.index'));
});

Breadcrumbs::register('admin.art.create', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->parent('admin.art.index');
    $breadcrumbs->push('Новая новость творческой мастерской', route('admin.art.create'));
});

Breadcrumbs::register('admin.art.edit', function (BreadcrumbsGenerator $breadcrumbs, $model) {
    $breadcrumbs->parent('admin.art.index');
    $breadcrumbs->push($model->title ?? '---', route('admin.art.edit', [$model]));
});
// ---------------------------

// Поддержка РПЦ
Breadcrumbs::register('admin.churches.index', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->push('Поддержка РПЦ', route('admin.churches.index'));
});

Breadcrumbs::register('admin.churches.create', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->parent('admin.churches.index');
    $breadcrumbs->push('Новая новость поддержки РПЦ', route('admin.churches.create'));
});

Breadcrumbs::register('admin.churches.edit', function (BreadcrumbsGenerator $breadcrumbs, $model) {
    $breadcrumbs->parent('admin.churches.index');
    $breadcrumbs->push($model->title ?? '---', route('admin.churches.edit', [$model]));
});
// ---------------------------

// ИЗДАТЕЛЬСКАЯ ДЕЯТЕЛЬНОСТЬ
Breadcrumbs::register('admin.publishings.index', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->push('Издательская деятельность', route('admin.publishings.index'));
});

Breadcrumbs::register('admin.publishings.create', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->parent('admin.publishings.index');
    $breadcrumbs->push('Новая новость издательской деятельности', route('admin.publishings.create'));
});

Breadcrumbs::register('admin.publishings.edit', function (BreadcrumbsGenerator $breadcrumbs, $model) {
    $breadcrumbs->parent('admin.publishings.index');
    $breadcrumbs->push($model->title ?? '---', route('admin.publishings.edit', [$model]));
});
// ---------------------------

// ПОМОЩЬ ДЕТЯМ
Breadcrumbs::register('admin.orphans.index', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->push('Помощь детям', route('admin.orphans.index'));
});

Breadcrumbs::register('admin.orphans.create', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->parent('admin.orphans.index');
    $breadcrumbs->push('Новая новость помощи детям', route('admin.orphans.create'));
});

Breadcrumbs::register('admin.orphans.edit', function (BreadcrumbsGenerator $breadcrumbs, $model) {
    $breadcrumbs->parent('admin.orphans.index');
    $breadcrumbs->push($model->title ?? '---', route('admin.orphans.edit', [$model]));
});
// ---------------------------

// ПОМОЩЬ ВЕТЕРАНАМ ВОЙН
Breadcrumbs::register('admin.veterans.index', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->push('Помощь ветеранам войн', route('admin.veterans.index'));
});

Breadcrumbs::register('admin.veterans.create', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->parent('admin.veterans.index');
    $breadcrumbs->push('Новая новость помощи ветеранам войн', route('admin.veterans.create'));
});

Breadcrumbs::register('admin.veterans.edit', function (BreadcrumbsGenerator $breadcrumbs, $model) {
    $breadcrumbs->parent('admin.veterans.index');
    $breadcrumbs->push($model->title ?? '---', route('admin.veterans.edit', [$model]));
});
// ---------------------------

// ЦИТАТЫ
Breadcrumbs::register('admin.quotes.index', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->push('Цитаты', route('admin.quotes.index'));
});

Breadcrumbs::register('admin.quotes.create', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->parent('admin.quotes.index');
    $breadcrumbs->push('Новая цитата', route('admin.quotes.create'));
});

Breadcrumbs::register('admin.quotes.edit', function (BreadcrumbsGenerator $breadcrumbs, $model) {
    $breadcrumbs->parent('admin.quotes.index');
    $breadcrumbs->push($model->author ? "#{$model->id}, {$model->author}" : '---', route('admin.quotes.edit', [$model]));
});
// ---------------------------

// ПАРТНЁРЫ
Breadcrumbs::register('admin.partners.index', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->push('Партнёры', route('admin.partners.index'));
});

Breadcrumbs::register('admin.partners.create', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->parent('admin.partners.index');
    $breadcrumbs->push('Новый партнёр', route('admin.partners.create'));
});

Breadcrumbs::register('admin.partners.edit', function (BreadcrumbsGenerator $breadcrumbs, $model) {
    $breadcrumbs->parent('admin.partners.index');
    $breadcrumbs->push($model->title ?? '---', route('admin.partners.edit', [$model]));
});
// ---------------------------

// СТРАНИЦЫ
Breadcrumbs::register('admin.pages.index', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->push('Страницы', route('admin.pages.index'));
});

Breadcrumbs::register('admin.pages.create', function (BreadcrumbsGenerator $breadcrumbs) {
    $breadcrumbs->parent('admin.pages.index');
    $breadcrumbs->push('Новая страница', route('admin.pages.create'));
});

Breadcrumbs::register('admin.pages.edit', function (BreadcrumbsGenerator $breadcrumbs, $model) {
    $breadcrumbs->parent('admin.pages.index');
    $breadcrumbs->push($model->title ?? '---', route('admin.pages.edit', [$model]));
});

Breadcrumbs::register('admin.pages.pages_galleries.index', function (BreadcrumbsGenerator $breadcrumbs, $parent) {
    $breadcrumbs->parent('admin.pages.edit', $parent);
    $breadcrumbs->push('Галлереи', route('admin.pages.pages_galleries.index', [$parent]));
});

Breadcrumbs::register('admin.pages.pages_galleries.create', function (BreadcrumbsGenerator $breadcrumbs, $parent) {
    $breadcrumbs->parent('admin.pages.pages_galleries.index', $parent);
    $breadcrumbs->push('Новая галлерея', route('admin.pages.pages_galleries.create', [$parent]));
});

Breadcrumbs::register('admin.pages.pages_galleries.edit', function (BreadcrumbsGenerator $breadcrumbs, $parent, $model) {
    $breadcrumbs->parent('admin.pages.pages_galleries.index', $parent);
    $breadcrumbs->push($model->key ?? '---', route('admin.pages.pages_galleries.edit', [$parent, $model]));
});
// ---------------------------