<?php

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PagesTableSeeder extends Seeder
{
    /**
     * @var \Illuminate\Database\DatabaseManager
     */
    private $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * Seed the application's database.
     *
     * @return void
     * @throws \Throwable
     */
    public function run()
    {
        $connection = $this->db->connection();
        $connection->transaction(function () use ($connection) {
            $now = Carbon::now('Europe/Moscow')->format('Y-m-d H:i:s');
            $connection->query()->from('pages')->insert([
                [
                    'id' => 1,
                    'parent_page_id' => null,
                    'view' => null,
                    'menu' => null,
                    'position' => null,
                    'title' => 'Шаблон',
                    'class' => null,

                    'content_0' => html_entity_decode(file_get_contents(__DIR__ . '/pages/1/0.html')),
                    'content_1' => html_entity_decode(file_get_contents(__DIR__ . '/pages/1/1.html')),
                    'content_2' => html_entity_decode(file_get_contents(__DIR__ . '/pages/1/2.html')),
                    'content_3' => html_entity_decode(file_get_contents(__DIR__ . '/pages/1/3.html')),
                    'content_4' => html_entity_decode(file_get_contents(__DIR__ . '/pages/1/4.html')),

                    'url' => null,
                    'title_seo' => null,
                    'description_seo' => null,
                    'keywords_seo' => null,

                    'is_active' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 2,
                    'parent_page_id' => null,
                    'view' => 'pages.2',
                    'menu' => null,
                    'position' => null,
                    'title' => 'Страница не найдена',
                    'class' => null,

                    'content_0' => null,
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => null,
                    'title_seo' => 'АНО «Связь Поколений» | Страница не найдена',
                    'description_seo' => 'АНО «Связь Поколений» | Страница не найдена',
                    'keywords_seo' => 'АНО «Связь Поколений» | Страница не найдена',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 3,
                    'parent_page_id' => null,
                    'view' => 'pages.3',
                    'menu' => 'Наши проекты',
                    'position' => 3000,
                    'title' => 'Наши проекты',
                    'class' => null,

                    'content_0' => null,
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '/',
                    'title_seo' => 'АНО «Связь Поколений» | Наши проекты',
                    'description_seo' => 'АНО «Связь Поколений» | Наши проекты',
                    'keywords_seo' => 'АНО «Связь Поколений» | Наши проекты',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 4,
                    'parent_page_id' => null,
                    'view' => 'pages.simple',
                    'menu' => 'О нас',
                    'position' => 4000,
                    'title' => 'О нас',
                    'class' => null,

                    'content_0' => html_entity_decode(file_get_contents(__DIR__ . '/pages/4/0.html')),
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '/' . str_slug('О нас'),
                    'title_seo' => 'АНО «Связь Поколений» | О нас',
                    'description_seo' => 'АНО «Связь Поколений» | О нас',
                    'keywords_seo' => 'АНО «Связь Поколений» | О нас',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 5,
                    'parent_page_id' => null,
                    'view' => 'pages.simple',
                    'menu' => 'Благодарности',
                    'position' => 5000,
                    'title' => 'Благодарности',
                    'class' => null,

                    'content_0' => html_entity_decode(file_get_contents(__DIR__ . '/pages/5/0.html')),
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '/' . str_slug('Благодарности'),
                    'title_seo' => 'АНО «Связь Поколений» | Благодарности',
                    'description_seo' => 'АНО «Связь Поколений» | Благодарности',
                    'keywords_seo' => 'АНО «Связь Поколений» | Благодарности',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 6,
                    'parent_page_id' => null,
                    'view' => 'pages.simple',
                    'menu' => 'Документация',
                    'position' => 6000,
                    'title' => 'Документация',
                    'class' => null,

                    'content_0' => html_entity_decode(file_get_contents(__DIR__ . '/pages/6/0.html')),
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '/' . str_slug('Документация'),
                    'title_seo' => 'АНО «Связь Поколений» | Документация',
                    'description_seo' => 'АНО «Связь Поколений» | Документация',
                    'keywords_seo' => 'АНО «Связь Поколений» | Документация',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 7,
                    'parent_page_id' => null,
                    'view' => 'pages.list',
                    'menu' => 'Новости',
                    'position' => 7000,
                    'title' => 'Новости',
                    'class' => null,

                    'content_0' => html_entity_decode(file_get_contents(__DIR__ . '/pages/7/0.html')),
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '/' . str_slug('Новости'),
                    'title_seo' => 'АНО «Связь Поколений» | Новости',
                    'description_seo' => 'АНО «Связь Поколений» | Новости',
                    'keywords_seo' => 'АНО «Связь Поколений» | Новости',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 8,
                    'parent_page_id' => null,
                    'view' => 'pages.8',
                    'menu' => 'Партнёры',
                    'position' => 8000,
                    'title' => 'Партнёры',
                    'class' => null,

                    'content_0' => html_entity_decode(file_get_contents(__DIR__ . '/pages/8/0.html')),
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '/' . str_slug('Партнёры'),
                    'title_seo' => 'АНО «Связь Поколений» | Партнёры',
                    'description_seo' => 'АНО «Связь Поколений» | Партнёры',
                    'keywords_seo' => 'АНО «Связь Поколений» | Партнёры',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 9,
                    'parent_page_id' => null,
                    'view' => 'pages.9',
                    'menu' => 'Контакты',
                    'position' => 9000,
                    'title' => 'Контакты',
                    'class' => null,

                    'content_0' => html_entity_decode(file_get_contents(__DIR__ . '/pages/9/0.html')),
                    'content_1' => html_entity_decode(file_get_contents(__DIR__ . '/pages/9/1.html')),
                    'content_2' => html_entity_decode(file_get_contents(__DIR__ . '/pages/9/2.html')),
                    'content_3' => html_entity_decode(file_get_contents(__DIR__ . '/pages/9/3.html')),
                    'content_4' => html_entity_decode(file_get_contents(__DIR__ . '/pages/9/4.html')),

                    'url' => '/' . str_slug('Контакты'),
                    'title_seo' => 'АНО «Связь Поколений» | Контакты',
                    'description_seo' => 'АНО «Связь Поколений» | Контакты',
                    'keywords_seo' => 'АНО «Связь Поколений» | Контакты',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 10,
                    'parent_page_id' => 7,
                    'view' => 'pages.10',
                    'menu' => null,
                    'position' => 10000,
                    'title' => '{news.title}',
                    'class' => null,

                    'content_0' => null,
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '{parentUrl}/{news}-{newsSlug}',
                    'title_seo' => 'АНО «Связь Поколений» | {news.title}',
                    'description_seo' => 'АНО «Связь Поколений» | {news.title}',
                    'keywords_seo' => 'АНО «Связь Поколений» | {news.title}',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 11,
                    'parent_page_id' => 3,
                    'view' => 'pages.tile',
                    'menu' => 'Поддержка Русской Православной Церкви',
                    'position' => 11000,
                    'title' => html_entity_decode('Поддержка Русской&nbsp;Православной&nbsp;Церкви'),
                    'class' => 'church',

                    'content_0' => html_entity_decode(file_get_contents(__DIR__ . '/pages/11/0.html')),
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '/' . str_slug('Поддержка Русской Православной Церкви'),
                    'title_seo' => 'АНО «Связь Поколений» | Поддержка Русской Православной Церкви',
                    'description_seo' => 'АНО «Связь Поколений» | Поддержка Русской Православной Церкви',
                    'keywords_seo' => 'АНО «Связь Поколений» | Поддержка Русской Православной Церкви',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 12,
                    'parent_page_id' => 3,
                    'view' => 'pages.tile',
                    'menu' => 'Издательская деятельность',
                    'position' => 12000,
                    'title' => 'Издательская деятельность',
                    'class' => 'book',

                    'content_0' => html_entity_decode(file_get_contents(__DIR__ . '/pages/12/0.html')),
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '/' . str_slug('Издательская деятельность'),
                    'title_seo' => 'АНО «Связь Поколений» | Издательская деятельность',
                    'description_seo' => 'АНО «Связь Поколений» | Издательская деятельность',
                    'keywords_seo' => 'АНО «Связь Поколений» | Издательская деятельность',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 13,
                    'parent_page_id' => 3,
                    'view' => 'pages.tile',
                    'menu' => 'Помощь детям-сиротам и больным детям',
                    'position' => 13000,
                    'title' => 'Помощь детям-сиротам и больным детям',
                    'class' => 'horse',

                    'content_0' => html_entity_decode(file_get_contents(__DIR__ . '/pages/13/0.html')),
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '/' . str_slug('Помощь детям-сиротам и больным детям'),
                    'title_seo' => 'АНО «Связь Поколений» | Помощь детям-сиротам и больным детям',
                    'description_seo' => 'АНО «Связь Поколений» | Помощь детям-сиротам и больным детям',
                    'keywords_seo' => 'АНО «Связь Поколений» | Помощь детям-сиротам и больным детям',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 14,
                    'parent_page_id' => 3,
                    'view' => 'pages.tile',
                    'menu' => 'Помощь ветеранам войн',
                    'position' => 14000,
                    'title' => 'Помощь ветеранам войн',
                    'class' => 'veteran',

                    'content_0' => html_entity_decode(file_get_contents(__DIR__ . '/pages/14/0.html')),
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '/' . str_slug('Помощь ветеранам войн'),
                    'title_seo' => 'АНО «Связь Поколений» | Помощь ветеранам войн',
                    'description_seo' => 'АНО «Связь Поколений» | Помощь ветеранам войн',
                    'keywords_seo' => 'АНО «Связь Поколений» | Помощь ветеранам войн',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 15,
                    'parent_page_id' => 3,
                    'view' => 'pages.tile',
                    'menu' => 'Творческая мастерская',
                    'position' => 15000,
                    'title' => 'Творческая мастерская',
                    'class' => 'art',

                    'content_0' => html_entity_decode(file_get_contents(__DIR__ . '/pages/15/0.html')),
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '/' . str_slug('Творческая мастерская'),
                    'title_seo' => 'АНО «Связь Поколений» | Творческая мастерская',
                    'description_seo' => 'АНО «Связь Поколений» | Творческая мастерская',
                    'keywords_seo' => 'АНО «Связь Поколений» | Творческая мастерская',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 16,
                    'parent_page_id' => 11,
                    'view' => 'pages.item',
                    'menu' => null,
                    'position' => 16000,
                    'title' => '{churches.title}',
                    'class' => null,

                    'content_0' => null,
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '{parentUrl}/{churches}-{churchesSlug}',
                    'title_seo' => 'АНО «Связь Поколений» | {churches.title}',
                    'description_seo' => 'АНО «Связь Поколений» | {churches.title}',
                    'keywords_seo' => 'АНО «Связь Поколений» | {churches.title}',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 17,
                    'parent_page_id' => 12,
                    'view' => 'pages.item',
                    'menu' => null,
                    'position' => 17000,
                    'title' => '{publishings.title}',
                    'class' => null,

                    'content_0' => null,
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '{parentUrl}/{publishings}-{publishingsSlug}',
                    'title_seo' => 'АНО «Связь Поколений» | {publishings.title}',
                    'description_seo' => 'АНО «Связь Поколений» | {publishings.title}',
                    'keywords_seo' => 'АНО «Связь Поколений» | {publishings.title}',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 18,
                    'parent_page_id' => 13,
                    'view' => 'pages.item',
                    'menu' => null,
                    'position' => 18000,
                    'title' => '{orphans.title}',
                    'class' => null,

                    'content_0' => null,
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '{parentUrl}/{orphans}-{orphansSlug}',
                    'title_seo' => 'АНО «Связь Поколений» | {orphans.title}',
                    'description_seo' => 'АНО «Связь Поколений» | {orphans.title}',
                    'keywords_seo' => 'АНО «Связь Поколений» | {orphans.title}',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 19,
                    'parent_page_id' => 14,
                    'view' => 'pages.item',
                    'menu' => null,
                    'position' => 19000,
                    'title' => '{veterans.title}',
                    'class' => null,

                    'content_0' => null,
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '{parentUrl}/{veterans}-{veteransSlug}',
                    'title_seo' => 'АНО «Связь Поколений» | {veterans.title}',
                    'description_seo' => 'АНО «Связь Поколений» | {veterans.title}',
                    'keywords_seo' => 'АНО «Связь Поколений» | {veterans.title}',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id' => 20,
                    'parent_page_id' => 15,
                    'view' => 'pages.item',
                    'menu' => null,
                    'position' => 20000,
                    'title' => '{art.title}',
                    'class' => null,

                    'content_0' => null,
                    'content_1' => null,
                    'content_2' => null,
                    'content_3' => null,
                    'content_4' => null,

                    'url' => '{parentUrl}/{art}-{artSlug}',
                    'title_seo' => 'АНО «Связь Поколений» | {art.title}',
                    'description_seo' => 'АНО «Связь Поколений» | {art.title}',
                    'keywords_seo' => 'АНО «Связь Поколений» | {art.title}',

                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        });
    }
}