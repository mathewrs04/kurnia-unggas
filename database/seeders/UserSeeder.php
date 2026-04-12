<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pemilik Usaha
        User::create([
            'name' => 'Pemilik Usaha',
            'email' => 'pemilik@kurniaunggas.com',
            'password' => Hash::make('password123'),
            'role' => 'pemilik',
        ]);

        // Penanggung Jawab Usaha
        User::create([
            'name' => 'Penanggung Jawab',
            'email' => 'pj@kurniaunggas.com',
            'password' => Hash::make('password123'),
            'role' => 'penanggung_jawab',
        ]);

        // Kasir
        User::create([
            'name' => 'Kasir',
            'email' => 'kasir@kurniaunggas.com',
            'password' => Hash::make('password123'),
            'role' => 'kasir',
        ]);
    }
}
