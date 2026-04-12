<?php

namespace App\Http\Controllers;

use App\Models\BiayaOperasional;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class LaporanKeuntunganController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);

        $bulanList = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $rows = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $totalPenjualan = Penjualan::whereYear('tanggal_jual', $tahun)
                ->whereMonth('tanggal_jual', $bulan)
                ->sum('subtotal');

            $totalPembelian = PembelianDetail::whereHas('pembelian', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_pembelian', $tahun)
                  ->whereMonth('tanggal_pembelian', $bulan);
            })->sum('subtotal');

            $totalBiayaOperasional = BiayaOperasional::whereYear('tanggal_biaya', $tahun)
                ->whereMonth('tanggal_biaya', $bulan)
                ->sum('subtotal');

            $labaRugi = $totalPenjualan - $totalPembelian - $totalBiayaOperasional;

            $rows[$bulan] = [
                'nama_bulan'            => $bulanList[$bulan],
                'total_penjualan'       => $totalPenjualan,
                'total_pembelian'       => $totalPembelian,
                'total_biaya_operasional' => $totalBiayaOperasional,
                'laba_rugi'             => $labaRugi,
            ];
        }

        $grandTotalPenjualan        = collect($rows)->sum('total_penjualan');
        $grandTotalPembelian        = collect($rows)->sum('total_pembelian');
        $grandTotalBiayaOperasional = collect($rows)->sum('total_biaya_operasional');
        $grandLabaRugi              = $grandTotalPenjualan - $grandTotalPembelian - $grandTotalBiayaOperasional;

        // Rentang tahun untuk dropdown (5 tahun ke belakang s/d tahun ini)
        $tahunList = range(now()->year, now()->year - 5);

        return view('report.keuntungan', compact(
            'rows',
            'tahun',
            'tahunList',
            'grandTotalPenjualan',
            'grandTotalPembelian',
            'grandTotalBiayaOperasional',
            'grandLabaRugi'
        ));
    }
}
