<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            PenjualanAyamHarian2024Seeder::class,
        ]);
    }
}
