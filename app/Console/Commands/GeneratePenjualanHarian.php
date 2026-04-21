<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GeneratePenjualanHarian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-penjualan-harian';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = DB::table('penjualans')
            ->join('penjualan_details', 'penjualans.id', '=', 'penjualan_details.penjualan_id')
            ->where('penjualan_details.produk_id', 1) // ayam
            ->selectRaw('
                DATE(tanggal_jual) as tanggal,
                SUM(jumlah_ekor) as total_ekor
            ')
            ->groupByRaw('DATE(tanggal_jual)')
            ->get();

        foreach ($data as $row) {
            DB::table('penjualan_ayam_harians')->updateOrInsert(
                ['tanggal' => $row->tanggal],
                [
                    'total_ekor' => $row->total_ekor,
                    'updated_at' => now(),
                ]
            );
        }

        $this->info('Penjualan ayam harian berhasil diperbarui!');
    }
    
}
