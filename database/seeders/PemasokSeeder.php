<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PemasokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 0; $i < 10; $i++) {
            \DB::table('pemasoks')->insert([
                'nama_pabrik' => $faker->company,
                'nama_marketing' => $faker->name,
                'no_telp_marketing' => $faker->phoneNumber
            ]);
        }
    }
}
