<?php

namespace App\Console\Commands;

use App\Models\ForecastPenjualans;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ForecastSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * 
     */
    protected $signature = 'forecast:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate forecast for 365 days (training must be done first)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Jika belum training, beri pesan
        $this->info('Memulai generate prediksi untuk 365 hari...');
        
        $response = Http::timeout(120)->get('http://127.0.0.1:8000/predict', ['days' => 365]);
        
        if (!$response->successful()) {
            $this->error('Gagal terhubung ke AI server. Pastikan server Python running dan model sudah dilatih.');
            return 1;
        }
        
        $rows = $response->json('data');
        if (empty($rows)) {
            $this->error('Data prediksi kosong.');
            return 1;
        }
        
        DB::transaction(function () use ($rows) {
            foreach ($rows as $r) {
                ForecastPenjualans::create([
                    'tanggal' => $r['ds'],
                    'prediksi' => round($r['yhat']),
                    'lower' => round($r['yhat_lower']),
                    'upper' => round($r['yhat_upper']),
                ]);
            }
        });
        
        $this->info('Prediksi untuk 365 hari berhasil disimpan. Silakan buka halaman forecast untuk melihat evaluasi.');
        return 0;
    }
    }

