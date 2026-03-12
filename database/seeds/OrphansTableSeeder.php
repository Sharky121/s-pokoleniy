<?php

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrphansTableSeeder extends Seeder
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
            foreach ($connection->select('select * from articles where category = ?', ['orphans']) as $record) {
                $connection->query()->from('orphans')->insert([
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

                    $connection->query()->from('orphans_photos')->insert([
                        'id' => $j,
                        'path' => "/images/{$image}.jpg",
                        'position' => $k,
                        'orphan_id' => $i,
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