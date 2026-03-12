<?php

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ChurchesTableSeeder extends Seeder
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

            $i = 1;
            $j = 1;
            $records = $connection->select('select * from articles where category = ?', ['church']);
            $records[] = (object)[
                'title' => 'Храм святого благоверного князя Димитрия Донского',
                'place' => null,
                'date' => '2012.10',
                'content' => <<< HTML
<p>23 октября 2012 года состоялся чин Великого освящения храма святого благоверного князя Димитрия Донского при ОМОН по г. Москве, построенного при поддержке АНО &laquo;Связь Поколений&raquo;</p>
HTML
                ,
                'images' => '4b5269da3d4149293da4c5f9f7c6aec7,6f59f4a6d14f3a67ae66ae99f94e8503,e4f7e6286932efccedcc165d3bb89127,header_bg2',
                'cover' => '4b5269da3d4149293da4c5f9f7c6aec7',
            ];
            $records[] = (object)[
                'title' => 'Париж',
                'place' => null,
                'date' => '2018.05',
                'content' => <<< HTML
<p>05 мая 2018 года руководство АНО &laquo;Связь Поколений&raquo; организовало в Российском духовно-культурном православном центре в Париже презентацию учебно-методического пособия &laquo;Жизнь и Учение Господа Иисуса Христа&raquo; Диакона Ильи Кокина и выставку работ священника Дмитрия Ершова.</p>
HTML
                ,
                'images' => 'IMG-20191024-WA0010,IMG-20191024-WA0011',
                'cover' => 'IMG-20191024-WA0010',
            ];
            $records[] = (object)[
                'title' => 'Паломничество в г. Тула',
                'place' => null,
                'date' => '2018.09',
                'content' => <<< HTML
<p>10 сентября 2018 года АНО &laquo;Связь Поколений&raquo; совместно с ООО &laquo;Ключ Здоровья&raquo; организовало бесплатные автобусы из разных частей Тульской области для паломников, желающих приложиться к мощам Святителя Спиридона Тримифунтского, привезенных в г. Тулу благотворительным фондом равноапостольного князя Владимира.</p>
HTML
                ,
                'images' => 'IMG-20191024-WA0012,IMG-20191024-WA0013,IMG-20191024-WA0014',
                'cover' => 'IMG-20191024-WA0012',
            ];

            foreach ($records as $record) {
                if (($record->id ?? null) == 1) {
                    // Правки от 2019-11-01
                    $record->content = <<< HTML
<p>15 октября 2018 года в г. Карачи на территории Консульства РФ в Пакистане Президентом АНО &laquo;Связь Поколений&raquo; - Рязанцевым Сергеем Михайловичем совместно с Генеральным Консулом Хозиным Александром Григорьевичем и ответственным секретарем отдела по благотворительности и социальному служению Московского Патриархата Игуменом Серафимом (Кравченко) был заложен домовой Храм в честь Святого Праведного Федора Ушакова.</p>
HTML;
                }

                if (($record->id ?? null) == 2) {
                    // Правки от 2019-11-01
                    $record->title = 'Исламабад';
                    $record->cover = '2_1';
                    $record->images = '2_1,' . $record->images;
                    $record->content = <<< HTML
<p>13 ноября 2016 года был освящен, построенный на средства АНО &laquo;Связь Поколений&raquo; храм в честь Покрова Пресвятой Богородицы при Посольстве Российской Федерации в Исламабаде. Чин освящения совершил по благословению Святейшего Патриарха Московского и всея Руси Кирилла - епископ Богородский Антоний, руководитель Управления по зарубежным учреждениям Московской Патриархии, в сослужении игумена Серафима (Кравченко) и диакона Сергия Калашникова.</p>
HTML;
                }

                if (($record->id ?? null) == 3) {
                    // Правки от 2019-11-01
                    $record->content = <<< HTML
<p>30 сентября 2017 года пришла встреча Президента АНО &laquo;Связь Поколений&raquo; - Рязанцева Сергея Михайловича с руководством Федеральной Службой Охраны Президента РФ и Гаражом Особого Назначения, на котором было принято решение об оказании помощи в строительстве Храма-Часовни в честь Димитрия Солунского на территории полигона гаража особого назначения ФСО РФ.</p>
HTML;
                }

                if (($record->id ?? null) == 4) {
                    // Правки от 2019-11-01
                    $record->content = <<< HTML
<p>05 декабря 2018 года Президент АНО &laquo;Связь Поколений&raquo; - Рязанцев Сергей Михайлович пожертвовал Храму пророка Божия Ильи во 2-ом Обыденском переулке Запрестольный образ Воскресения Христова, написанный известным современным Российским художником Никитой Владимировичем Нужным.</p>
HTML;
                }

                $connection->query()->from('churches')->insert([
                    'id' => $i,
                    'title' => html_entity_decode($record->title),
                    'place' => html_entity_decode($record->place),
                    'date' => Carbon::parse(str_before($record->date, '.') . '-' . str_after($record->date, '.') . '-01 00:00:00', 'Europe/Moscow'),
                    'content_short' => Str::words(strip_tags(html_entity_decode($record->content)), 20),
                    'content_long' => html_entity_decode($record->content),
                    'cover' => "/images/{$record->cover}.jpg",
                    'is_active' => 1,
                    'url' => $i . '-' . str_slug(html_entity_decode($record->title)),
                    'title_seo' => html_entity_decode($record->title),
                    'description_seo' => Str::words(strip_tags(html_entity_decode($record->content)), 20),
                    'keywords_seo' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Делаем thumb для обложки
                if (file_exists(public_path("/images/previews/{$record->cover}.png"))) {
                    $img = Image::make(public_path("/images/previews/{$record->cover}.png"));
                    $img->save(public_path("/images/{$record->cover}.thumb.jpg"));
                }

                // Импортируем изображения + делаем thumb'ы для них
                $images = explode(',', $record->images);
                foreach ($images as $k => $image) {
                    if (file_exists(public_path("/images/previews/{$image}.png"))) {
                        $img = Image::make(public_path("/images/previews/{$image}.png"));
                        $img->save(public_path("/images/{$image}.thumb.jpg"));
                    }

                    $connection->query()->from('churches_photos')->insert([
                        'id' => $j,
                        'path' => "/images/{$image}.jpg",
                        'position' => $k,
                        'church_id' => $i,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    $j++;
                }

                $i++;
            }
        });
    }
}