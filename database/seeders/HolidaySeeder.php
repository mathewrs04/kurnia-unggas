<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =========================
        // HAPUS DATA LAMA
        // =========================
        DB::table('holidays')->truncate();

        // =========================
        // DATA HARI BESAR
        // =========================
        $data = [

            // ===== LEBARAN =====
            [
                'name' => 'lebaran',
                'date' => '2022-05-02',
                'pre_days' => 3,
                'post_days' => 6,
            ],
            [
                'name' => 'lebaran',
                'date' => '2023-04-22',
                'pre_days' => 3,
                'post_days' => 6,
            ],
            [
                'name' => 'lebaran',
                'date' => '2024-04-10',
                'pre_days' => 3,
                'post_days' => 6,
            ],
            [
                'name' => 'lebaran',
                'date' => '2025-03-31',
                'pre_days' => 3,
                'post_days' => 6,
            ],


            // ===== TAHUN BARU =====
            [
                'name' => 'tahun_baru',
                'date' => '2022-01-01',
                'pre_days' => 2,
                'post_days' => 0,
            ],
            [
                'name' => 'tahun_baru',
                'date' => '2023-01-01',
                'pre_days' => 2,
                'post_days' => 0,
            ],
            [
                'name' => 'tahun_baru',
                'date' => '2024-01-01',
                'pre_days' => 2,
                'post_days' => 0,
            ],
            [
                'name' => 'tahun_baru',
                'date' => '2025-01-01',
                'pre_days' => 2,
                'post_days' => 0,
            ],
        ];

        // =========================
        // INSERT DATA
        // =========================
        foreach ($data as $row) {
            DB::table('holidays')->insert([
                'name' => $row['name'],
                'date' => $row['date'],
                'pre_days' => $row['pre_days'],
                'post_days' => $row['post_days'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
