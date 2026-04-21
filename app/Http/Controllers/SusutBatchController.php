<?php

namespace App\Http\Controllers;

use App\Models\BatchPembelian;

class SusutBatchController extends Controller
{
    public function index()
    {
        $batches = BatchPembelian::with(['stokOpnames', 'mortalitas', 'pembelianDetails.timbangan', 'penjualanDetails'])
            ->where('stok_ekor', '<=', 0)
            ->orderBy('kode_batch')
            ->get();

        foreach ($batches as $batch) {
            $detailAwal = $batch->pembelianDetails->sortBy('id')->first();
            $stokAwalKg = (float) ($detailAwal?->timbangan?->total_berat ?? 0);
            
            // Hitung total berat yang terjual dari penjualan detail batch ini
            $totalBeratTerjual = (float) $batch->penjualanDetails->sum('jumlah_berat');
            
            // Hitung total berat mortalitas (dianggap sebagai keluaran, bukan susut)
            $totalBeratMortalitas = (float) $batch->mortalitas->sum('berat_kg');
            
            // Hitung total susut dari stok opname (akumulasi jika ada beberapa kali opname)
            $totalSusutOpname = (float) $batch->stokOpnames->sum(function ($opname) {
                return max((float) $opname->susut_kg, 0);
            });

            $batch->stok_awal_kg = $stokAwalKg;
            $batch->total_berat_terjual = $totalBeratTerjual;
            $batch->total_berat_mortalitas = $totalBeratMortalitas;
            $batch->total_susut_opname_kg = $totalSusutOpname;
            // Susut total = berat awal - (total berat terjual + total berat mortalitas)
            $batch->susut_total_kg = $stokAwalKg - ($totalBeratTerjual + $totalBeratMortalitas);
        }

        return view('susut-batch.index', compact('batches'));
    }
}
