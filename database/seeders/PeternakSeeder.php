<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PeternakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        for ($i = 0; $i < 10; $i++) {
            \DB::table('peternaks')->insert([
                'nama' => $faker->name,
                'alamat' => $faker->address,
                'no_telepon' => $faker->phoneNumber,
                'pemasok_id' => $faker->numberBetween(1, 10)
            ]);
        }
    }
}
