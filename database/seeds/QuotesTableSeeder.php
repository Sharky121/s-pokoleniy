<?php

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class QuotesTableSeeder extends Seeder
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

            $quotes = [
                ['content' => html_entity_decode('Добродетель человека измеряется не необыкновенными подвигами, а его ежедневным усилием.'), 'author' => 'Блез Паскаль'],
                ['content' => html_entity_decode('Должно приучать себя к добродетельным делам и поступкам, а не к речам о добродетели.'), 'author' => 'Демокрит Абдерский'],
                ['content' => html_entity_decode('Истина, свобода и добродетель &ndash; вот единственное, ради чего нужно любить жизнь.'), 'author' => 'Вольтер'],
                ['content' => html_entity_decode('Мы постоянно восхищаемся всякими редкостями; почему же мы так равнодушны к добродетели?'), 'author' => 'Жан де Лабрюйер'],
                ['content' => html_entity_decode('Чтобы жить в добродетели, мы всегда должны вести борьбу с самими собой.'), 'author' => 'Жан-Жак Руссо'],
            ];

            foreach ($quotes as $i => $quote) {
                $connection->query()->from('quotes')->insert([
                    'id' => $i + 1,
                    'content' => $quote['content'],
                    'author' => $quote['author'],
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });
    }
}