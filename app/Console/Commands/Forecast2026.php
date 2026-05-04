<?php

namespace App\Console\Commands;

use App\Models\ForecastPenjualans;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class Forecast2026 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forecast:2026';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate forecast for full year 2026 using model trained with 2022-2025 data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Calculate total days from start training data (2022-01-01) to end of 2026
        // 2022: 365, 2023: 365, 2024: 366 (leap), 2025: 365, 2026: 365 = 1826 total days
        $startTraining = Carbon::create(2022, 1, 1, 0, 0, 0);
        $endForecast = Carbon::create(2026, 12, 31, 0, 0, 0);
        $totalDays = (int) $startTraining->diffInDays($endForecast) + 1;

        $this->info("Memulai generate prediksi untuk {$totalDays} hari (2022-2026 untuk ambil data 2026)...");
        
        try {
            $response = Http::timeout(120)->get('http://127.0.0.1:8000/predict', ['days' => $totalDays]);
        } catch (\Exception $e) {
            $this->error('Gagal terhubung ke AI server. Pastikan server Python running dan model sudah dilatih.');
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
        
        if (!$response->successful()) {
            $this->error('Gagal terhubung ke AI server. Server mengembalikan status: ' . $response->status());
            return 1;
        }
        
        $rows = $response->json('data');
        if (empty($rows)) {
            $this->error('Data prediksi kosong.');
            return 1;
        }

        // Filter hanya data untuk tahun 2026
        $rows2026 = collect($rows)->filter(function ($r) {
            $tanggal = Carbon::parse($r['ds']);
            return $tanggal->year == 2026;
        })->values()->toArray();

        if (count($rows2026) == 0) {
            $this->error('Tidak ada data prediksi untuk tahun 2026.');
            return 1;
        }
        
        DB::transaction(function () use ($rows2026) {
            foreach ($rows2026 as $r) {
                ForecastPenjualans::create([
                    'tanggal' => $r['ds'],
                    'prediksi' => round($r['yhat']),
                    'lower' => round($r['yhat_lower']),
                    'upper' => round($r['yhat_upper']),
                ]);
            }
        });
        
        $this->info('✓ Prediksi untuk ' . count($rows2026) . ' hari tahun 2026 berhasil disimpan.');
        return 0;
    }
}
