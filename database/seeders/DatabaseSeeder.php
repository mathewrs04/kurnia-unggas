<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PemasokSeeder::class,
            PeternakSeeder::class,
            PelangganSeeder::class,
            ProdukSeeder::class,
            HolidaySeeder::class,
            PenjualanAyamHarianSeeder::class,
            SimulasiJanuari2026Seeder::class,
        ]);
    }
}
