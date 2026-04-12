<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PenjualanAyamHarian2024Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // =========================
        // HAPUS DATA LAMA
        // =========================
        DB::table('penjualan_ayam_harians')->truncate();

        // =========================
        // DATA LEBARAN TIAP TAHUN
        // =========================
        $lebaranDates = [
            2022 => '2022-05-02',
            2023 => '2023-04-22',
            2024 => '2024-04-10',
            2025 => '2025-03-31',
        ];

        // =========================
        // LOOP TIAP TAHUN
        // =========================
        foreach ($lebaranDates as $year => $lebaranDate) {

            $start = Carbon::create($year,1,1);
            $end   = Carbon::create($year,12,31);
            $lebaran = Carbon::parse($lebaranDate);
            $tahunBaru = Carbon::create($year+1,1,1);

            while ($start->lte($end)) {

                $tanggal = $start->copy();
                $totalEkor = 0;

                // ======================
                // LEBARAN
                // ======================
                if ($tanggal->between(
                    $lebaran->copy()->subDays(3),
                    $lebaran->copy()->subDay()
                )) {

                    $totalEkor = rand(400,600);

                } elseif ($tanggal->between(
                    $lebaran,
                    $lebaran->copy()->addDays(6)
                )) {

                    $totalEkor = 0;

                // ======================
                // TAHUN BARU
                // ======================
                } elseif ($tanggal->between(
                    $tahunBaru->copy()->subDays(2),
                    $tahunBaru->copy()->subDay()
                )) {

                    $totalEkor = rand(300,400);

                // ======================
                // NORMAL
                // ======================
                } else {

                    // base normal + noise
                    $totalEkor = 100 + rand(-25,25);
                }

                DB::table('penjualan_ayam_harians')->insert([
                    'tanggal' => $tanggal->toDateString(),
                    'total_ekor' => $totalEkor,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $start->addDay();
            }
        }
    }
}
