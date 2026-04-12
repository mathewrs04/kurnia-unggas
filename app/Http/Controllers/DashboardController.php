<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\BiayaOperasional;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Data untuk pemilik usaha
        if ($user->isPemilik()) {
            return $this->dashboardPemilik();
        }

        // Data untuk penanggung jawab
        if ($user->isPenanggungJawab()) {
            return $this->dashboardPenanggungJawab();
        }

        // Data untuk kasir
        return $this->dashboardKasir();
    }

    private function dashboardPemilik()
    {
        // Ringkasan bulan ini
        $bulanIni = Carbon::now()->format('Y-m');
        
        $totalPenjualan = Penjualan::whereRaw("DATE_FORMAT(tanggal_jual, '%Y-%m') = ?", [$bulanIni])->sum('subtotal');
        $totalPembelian = Pembelian::whereRaw("DATE_FORMAT(tanggal_pembelian, '%Y-%m') = ?", [$bulanIni])
            ->with('pembelianDetails')->get()->sum(function($pembelian) {
                return $pembelian->pembelianDetails->sum('subtotal');
            });
        $totalBiayaOperasional = BiayaOperasional::whereRaw("DATE_FORMAT(tanggal_biaya, '%Y-%m') = ?", [$bulanIni])->sum('subtotal');

        $keuntungan = $totalPenjualan - ($totalPembelian + $totalBiayaOperasional);

        // Data grafik penjualan per bulan (6 bulan terakhir)
        $grafikPenjualan = Penjualan::select(
                DB::raw("DATE_FORMAT(tanggal_jual, '%Y-%m') as bulan"),
                DB::raw('SUM(subtotal) as total')
            )
            ->where('tanggal_jual', '>=', Carbon::now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Data grafik pembelian per bulan (6 bulan terakhir)
        $grafikPembelian = Pembelian::select(
                DB::raw("DATE_FORMAT(tanggal_pembelian, '%Y-%m') as bulan")
            )
            ->with('pembelianDetails')
            ->where('tanggal_pembelian', '>=', Carbon::now()->subMonths(6))
            ->get()
            ->groupBy('bulan')
            ->map(fn($items) => [
                'bulan' => $items[0]->bulan,
                'total' => $items->sum(fn($pembelian) => $pembelian->pembelianDetails->sum('subtotal'))
            ])
            ->values();

        return view('dashboard.pemilik', compact(
            'totalPenjualan',
            'totalPembelian',
            'totalBiayaOperasional',
            'keuntungan',
            'grafikPenjualan',
            'grafikPembelian'
        ));
    }

    private function dashboardPenanggungJawab()
    {
        // Dashboard umum untuk penanggung jawab
        return view('dashboard.penanggung-jawab');
    }

    private function dashboardKasir()
    {
        // Dashboard sederhana untuk kasir
        $penjualanHariIni = Penjualan::whereDate('tanggal_jual', Carbon::today())
            ->where('user_id', auth()->id())
            ->count();

        return view('dashboard.kasir', compact('penjualanHariIni'));
    }
}
