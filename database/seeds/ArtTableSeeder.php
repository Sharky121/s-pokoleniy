<?php

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ArtTableSeeder extends Seeder
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
            foreach ($connection->select('select * from articles where category = ?', ['art']) as $record) {
                if (($record->id ?? null) == 15) {
                    // Правки от 2019-11-01
                    $record->content = <<< HTML
<p>АНО &laquo;Связь Поколений&raquo;, Русский молодежный театр VNT и новорожденная эстонско-российская ассоциация &laquo;Диалог культур&raquo; запустили совместный проект 
&laquo;Театральная гостиная&raquo;. Ведущей первой встречи была петербургская актриса Юлия Рудакова, а гостем, собравшим полный зал, &ndash; композитор
 и бард, заслуженный деятель искусств РФ Григорий Гладков.</p>
 <p>Григория Гладкова любят слушатели всех возрастов: от дошколят, только-только встретившихся с классическим мультфильмом 
 Евгения Татарского &laquo;Пластилиновая ворона&raquo;, в котором звучит ставшая легендарной песня Гладкова об этой самой вороне, 
 до взрослых, для которых этот фильм &ndash; один из самых ностальгических воспоминаний о прошлом.</p>
 <p>Титулов у него &ndash; как у испанского гранда: член Союза композиторов, член Союза кинематографистов России, член Союза театральных 
 деятелей России, почётный член Академии образования России, почётный член Малой Академии наук России (&laquo;Интеллект будущего&raquo;), 
 Академик Детской Телевизионной Национальной Академии России, лауреат премии Фонда развития детского кино и телевидения имени Ролана Быкова, 
 лауреат Литературной премии Корнея Ивановича Чуковского, лауреат премии в области аудиовизуальных искусств Департамента культуры города 
 Москвы &laquo;Звёздный Мост&raquo;, лауреат национальной музыкальной гранд-премии &laquo;КиноВатсон&raquo;, лауреат высших наград в 
 области радио и телевидения в России &ndash; &laquo;Радиомания&raquo; и &laquo;Тэффи&raquo;. Он награждён &laquo;Российским комитетом 
 по регистрации рекордов планеты&raquo; (российское отделение книги рекордов Гиннеса) за издание в России самого большого количества 
 пластинок, кассет и компакт-дисков (для детей). 31 ноября 2011 года в созвездии &laquo;Северная Корона&raquo; зарегистрирована 
 звезда &laquo;Гладков Григорий&raquo; &ndash; регистрация № CrB18_00085 в &laquo;Каталоге небесных тел &laquo;Русского Астрономического 
 Общества&raquo;.</p><p>Но хотя у него есть собственная звезда, он начисто лишен признаков той звездной болезни, которой страдают в тяжелой 
 форме многочисленные поп-звезды весьма сомнительных достоинств.</p>
 <p>Григорий Гладков удивительно умеет общаться с детьми. И дети ценят это. Он пригласил на сцену малышей &ndash; и вел с ними разговор на равных. 
 Одна девочка из самой младшей возрастной группы театра VNT даже призналась, что терпеть не может принцесс. Театр собирается ставить сказку про 
 обезьянку Анфиску, которую написал Эдуард Успенский и в которой звучит песня Григория Гладкова, и маленькая актриса заявила, что именно 
 обезьянку мечтает сыграть.</p>
<p>Это самая интересная роль, &ndash; уважительно одобрил композитор.</p>
HTML;
                }

                if (($record->id ?? null) == 18) {
                    // Правки от 2019-11-01
                    $record->content = <<< HTML
<p>При поддержке АНО &laquo;Связь Поколений&raquo; 23 марта 2017 года в главном здании Театрального музея им. А.А. Бахрушина прошел творческий вечер «Русский крест Николая Мельникова. Судьба и творчество» с участием Юлии Рудаковой и ансамбля «Аккорд-стиль».</p>
<p>Из цикла &laquo;Музыкальная гостиная&raquo;.</p>
<p>В вечере принимают участие:
<br>Лауреат Премии имени Михаила Ломоносова Юлия Рудакова;
<br>Лауреат международных конкурсов Валерия Староверова;
<br>Ансамбль народных инструментов &laquo;Аккорд-стиль&raquo; по управлением Юрия Зиминова.</p>
HTML;
                }

                $connection->query()->from('art')->insert([
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

                    $connection->query()->from('art_photos')->insert([
                        'id' => $j,
                        'path' => "/images/{$image}.jpg",
                        'position' => $k,
                        'art_id' => $i,
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