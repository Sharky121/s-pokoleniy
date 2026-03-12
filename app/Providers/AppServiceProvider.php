<?php

namespace App\Providers;

use Exception;
use App\Models\Art;
use App\Models\News;
use App\Models\Page;
use App\Models\Quote;
use App\Models\Church;
use App\Models\Orphan;
use App\Models\Partner;
use App\Models\Veteran;
use App\Models\Publishing;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('pages', function () {
            return Page::with(['parent', 'children'])->get();
        });

        $this->app->singleton('activePages', function ($app) {
            return $app->make('pages')->where('is_active', 1);
        });

        $this->app->singleton('activeRootPages', function ($app) {
            return $app->make('activePages')->where('parent_page_id', null)->where('menu', '!=', null)->sortBy('position');
        });

        $this->app->singleton('activeNews', function ($app) {
            // Нужна двойная сортировка, так как дата предполагает наличие только месяца и года
            return News::active()->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        });

        $this->app->singleton('activeChurches', function ($app) {
            // Нужна двойная сортировка, так как дата предполагает наличие только месяца и года
            return Church::active()->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        });

        $this->app->singleton('activePublishings', function ($app) {
            // Нужна двойная сортировка, так как дата предполагает наличие только месяца и года
            return Publishing::active()->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        });

        $this->app->singleton('activeOrphans', function ($app) {
            // Нужна двойная сортировка, так как дата предполагает наличие только месяца и года
            return Orphan::active()->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        });

        $this->app->singleton('activeVeterans', function ($app) {
            // Нужна двойная сортировка, так как дата предполагает наличие только месяца и года
            return Veteran::active()->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        });

        $this->app->singleton('activeArt', function ($app) {
            // Нужна двойная сортировка, так как дата предполагает наличие только месяца и года
            return Art::active()->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        });

        $this->app->singleton('activeUnionArticles', function ($app) {
            $activeNews = $app->make('activeNews');
            $activeChurches = $app->make('activeChurches');
            $activePublishings = $app->make('activePublishings');
            $activeOrphans = $app->make('activeOrphans');
            $activeVeterans = $app->make('activeVeterans');
            $activeArt = $app->make('activeArt');

            $articles = collect();
            $articles = $articles->merge($activeNews);
            $articles = $articles->merge($activeChurches);
            $articles = $articles->merge($activePublishings);
            $articles = $articles->merge($activeOrphans);
            $articles = $articles->merge($activeVeterans);
            $articles = $articles->merge($activeArt);

            $articles = $articles->map(function ($article) {
                switch (true) {
                    case $article instanceof News:
                        $article->page_id = 10;
                        break;
                    case $article instanceof Church:
                        $article->page_id = 16;
                        break;
                    case $article instanceof Publishing:
                        $article->page_id = 17;
                        break;
                    case $article instanceof Orphan:
                        $article->page_id = 18;
                        break;
                    case $article instanceof Veteran:
                        $article->page_id = 19;
                        break;
                    case $article instanceof Art:
                        $article->page_id = 20;
                        break;
                    default:
                        throw new Exception('Неизвестный тип статьи');
                }

                return $article;
            });

            return $articles->sortBy('date', SORT_REGULAR, true);
        });

        $this->app->singleton('activeQuotes', function ($app) {
            return Quote::active()->orderBy('id', 'asc')->get();
        });

        $this->app->singleton('activePartners', function ($app) {
            return Partner::active()->orderBy('position', 'desc')->get();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
