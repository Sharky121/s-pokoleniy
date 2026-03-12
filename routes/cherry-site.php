<?php

Route::group(['namespace' => 'App\\Http\\Controllers\\Admin\\'], function () {
    // НОВОСТИ
    Route::resource('news', 'NewsController', ['as' => 'admin', 'parameters' => ['news' => 'model']]);

    Route::post('news/{parent}/photos', 'NewsPhotosController@batchStore')->name('admin.news_photos.batchStore');
    Route::patch('news/{parent}/photos', 'NewsPhotosController@batchUpdate')->name('admin.news_photos.batchUpdate');
    Route::delete('news/{parent}/photos/{model}', 'NewsPhotosController@destroy')->name('admin.news_photos.destroy');
    // ---------------------------

    // ТВОРЧЕСКАЯ МАСТЕРСКАЯ
    Route::resource('art', 'ArtController', ['as' => 'admin', 'parameters' => ['art' => 'model']]);

    Route::post('art/{parent}/photos', 'ArtPhotosController@batchStore')->name('admin.art_photos.batchStore');
    Route::patch('art/{parent}/photos', 'ArtPhotosController@batchUpdate')->name('admin.art_photos.batchUpdate');
    Route::delete('art/{parent}/photos/{model}', 'ArtPhotosController@destroy')->name('admin.art_photos.destroy');
    // ---------------------------

    // ВОССТАНОВЛЕНИЕ ХРАМОВ
    Route::resource('churches', 'ChurchesController', ['as' => 'admin', 'parameters' => ['churches' => 'model']]);

    Route::post('churches/{parent}/photos', 'ChurchesPhotosController@batchStore')->name('admin.churches_photos.batchStore');
    Route::patch('churches/{parent}/photos', 'ChurchesPhotosController@batchUpdate')->name('admin.churches_photos.batchUpdate');
    Route::delete('churches/{parent}/photos/{model}', 'ChurchesPhotosController@destroy')->name('admin.churches_photos.destroy');
    // ---------------------------

    // ИЗДАТЕЛЬСКАЯ ДЕЯТЕЛЬНОСТЬ
    Route::resource('publishings', 'PublishingsController', ['as' => 'admin', 'parameters' => ['publishings' => 'model']]);

    Route::post('publishings/{parent}/photos', 'PublishingsPhotosController@batchStore')->name('admin.publishings_photos.batchStore');
    Route::patch('publishings/{parent}/photos', 'PublishingsPhotosController@batchUpdate')->name('admin.publishings_photos.batchUpdate');
    Route::delete('publishings/{parent}/photos/{model}', 'PublishingsPhotosController@destroy')->name('admin.publishings_photos.destroy');
    // ---------------------------

    // ПОМОЩЬ ДЕТЯМ
    Route::resource('orphans', 'OrphansController', ['as' => 'admin', 'parameters' => ['orphans' => 'model']]);

    Route::post('orphans/{parent}/photos', 'OrphansPhotosController@batchStore')->name('admin.orphans_photos.batchStore');
    Route::patch('orphans/{parent}/photos', 'OrphansPhotosController@batchUpdate')->name('admin.orphans_photos.batchUpdate');
    Route::delete('orphans/{parent}/photos/{model}', 'OrphansPhotosController@destroy')->name('admin.orphans_photos.destroy');
    // ---------------------------

    // ПОМОЩЬ ВЕТЕРАНАМ ВОЙН
    Route::resource('veterans', 'VeteransController', ['as' => 'admin', 'parameters' => ['veterans' => 'model']]);

    Route::post('veterans/{parent}/photos', 'VeteransPhotosController@batchStore')->name('admin.veterans_photos.batchStore');
    Route::patch('veterans/{parent}/photos', 'VeteransPhotosController@batchUpdate')->name('admin.veterans_photos.batchUpdate');
    Route::delete('veterans/{parent}/photos/{model}', 'VeteransPhotosController@destroy')->name('admin.veterans_photos.destroy');
    // ---------------------------

    // ЦИТАТЫ
    Route::resource('quotes', 'QuotesController', ['as' => 'admin', 'parameters' => ['quotes' => 'model']]);
    // ---------------------------

    // ПАРТНЁРЫ
    Route::resource('partners', 'PartnersController', ['as' => 'admin', 'parameters' => ['partners' => 'model']]);
    // ---------------------------

    // СТРАНИЦЫ
    Route::resource('pages', 'PagesController', ['as' => 'admin', 'parameters' => ['pages' => 'model']]);

    Route::get('pages/{parent}/galleries', 'PagesGalleriesController@index')->name('admin.pages.pages_galleries.index');
    Route::get('pages/{parent}/galleries/create', 'PagesGalleriesController@create')->name('admin.pages.pages_galleries.create');
    Route::get('pages/{parent}/galleries/{model}', 'PagesGalleriesController@edit')->name('admin.pages.pages_galleries.edit');
    Route::post('pages/{parent}/galleries', 'PagesGalleriesController@store')->name('admin.pages.pages_galleries.store');
    Route::patch('pages/{parent}/galleries/{model}', 'PagesGalleriesController@update')->name('admin.pages.pages_galleries.update');
    Route::delete('pages/{parent}/galleries/{model}', 'PagesGalleriesController@destroy')->name('admin.pages.pages_galleries.destroy');

    Route::post('pages/{page}/galleries/{parent}/photos', 'PagesGalleriesPhotosController@batchStore')->name('admin.pages.pages_galleries.photos.batchStore');
    Route::patch('pages/{page}/galleries/{parent}/photos', 'PagesGalleriesPhotosController@batchUpdate')->name('admin.pages.pages_galleries.photos.batchUpdate');
    Route::delete('pages/{page}/galleries/{parent}/photos/{model}', 'PagesGalleriesPhotosController@destroy')->name('admin.pages.pages_galleries.photos.destroy');
    // ---------------------------
});