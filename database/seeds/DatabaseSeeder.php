<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ArtTableSeeder::class);
        $this->call(ChurchesTableSeeder::class);
        $this->call(OrphansTableSeeder::class);
        $this->call(PublishingsTableSeeder::class);
        $this->call(VeteransTableSeeder::class);
        $this->call(NewsTableSeeder::class);
        $this->call(QuotesTableSeeder::class);
        $this->call(PartnersTableSeeder::class);
        $this->call(PagesTableSeeder::class);
    }
}
