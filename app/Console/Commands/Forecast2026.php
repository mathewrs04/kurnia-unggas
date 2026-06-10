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
    protected $signature = 'forecast:generate-2026';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate forecast tahun 2026 (jalankan training 2026 terlebih dahulu)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetYear = 2026;
        $forecastDays = 730;

        $this->info("Generate prediksi {$targetYear} untuk horizon {$forecastDays} hari...");

        try {
            $response = Http::timeout(120)->get('http://127.0.0.1:8000/predict', ['days' => $forecastDays]);
        } catch (\Exception $e) {
            $this->error('Gagal terhubung ke AI server saat prediksi.');
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        if (!$response->successful()) {
            $this->error('Prediksi gagal. Server AI mengembalikan status: ' . $response->status());
            return 1;
        }

        $rows = $response->json('data');
        if (empty($rows)) {
            $this->error('Data prediksi kosong.');
            return 1;
        }

        // $rows2026 = collect($rows)->filter(function ($r) {
        //     return Carbon::parse($r['ds'])->year === 2026;
        // })->values();

        // if ($rows2026->isEmpty()) {
        //     $this->error('Tidak ada data prediksi untuk tahun 2026.');
        //     return 1;
        // }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $r) {
                ForecastPenjualans::updateOrCreate(
                    ['tanggal' => $r['ds']],
                    [
                        'prediksi' => round($r['yhat']),
                        'lower' => round($r['yhat_lower']),
                        'upper' => round($r['yhat_upper']),
                    ]
                );
            }
        });

        // $this->info('✓ Prediksi untuk ' . $rows->count() . ' hari tahun  berhasil disimpan.');

        return 0;
    }
}
