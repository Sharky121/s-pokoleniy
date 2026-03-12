<?php

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PartnersTableSeeder extends Seeder
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

            $partners = [
                ['title' => 'Дети Солнца', 'description' => 'Международный  благотворительный фонд содействия укреплению мира и дружбы', 'site' => 'www.дети-солнца.рф', 'cover' => '/images/partner_deti_solnca.jpg'],
                ['title' => 'Асфалия', 'description' => 'Юридическая компания', 'site' => 'www.асфалия-юк.рф', 'cover' => '/images/partner_asfalia.jpg'],
            ];

            foreach ($partners as $i => $partner) {
                $connection->query()->from('partners')->insert([
                    'id' => $i + 1,
                    'position' => ($i + 1) * 1000,
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ] + $partner);
            }
        });
    }
}