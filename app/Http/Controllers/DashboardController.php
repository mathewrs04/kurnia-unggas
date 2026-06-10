<?php

namespace App\Http\Controllers;

use App\Models\BatchPembelian;
use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\BiayaOperasional;
use App\Models\Setting;

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
            ->with('pembelianDetails')->get()->sum(function ($pembelian) {
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
        $pembelians = Pembelian::with('pembelianDetails')
            ->where('tanggal_pembelian', '>=', Carbon::now()->subMonths(6))
            ->get();

        $grafikPembelian = $pembelians
            ->groupBy(function ($p) {
                return Carbon::parse($p->tanggal_pembelian)->format('Y-m');
            })
            ->map(function ($items, $bulan) {
                $total = $items->sum(function ($pembelian) {
                    return $pembelian->pembelianDetails->sum('subtotal');
                });

                return [
                    'bulan' => $bulan,
                    'total' => (int) $total,
                ];
            })
            ->sortBy('bulan')
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
        // Peringatan stok minimal global
        $totalStokEkor = BatchPembelian::where('stok_ekor', '>', 0)->sum('stok_ekor');
        $stokMinimalGlobal = (int) Setting::get('stok_minimal_global', 0);
        if ($stokMinimalGlobal > 0 && $totalStokEkor < $stokMinimalGlobal) {
            session()->flash('warning_stok_global',
                'Peringatan! Total stok ayam (' . number_format($totalStokEkor) . ' ekor) berada di bawah batas minimal global (' . number_format($stokMinimalGlobal) . ' ekor).');
        }

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
        $pembelians = Pembelian::with('pembelianDetails')
            ->where('tanggal_pembelian', '>=', Carbon::now()->subMonths(6))
            ->get();

        $grafikPembelian = $pembelians
            ->groupBy(function ($p) {
                return Carbon::parse($p->tanggal_pembelian)->format('Y-m');
            })
            ->map(function ($items, $bulan) {
                $total = $items->sum(function ($pembelian) {
                    return $pembelian->pembelianDetails->sum('subtotal');
                });

                return [
                    'bulan' => $bulan,
                    'total' => (int) $total,
                ];
            })
            ->sortBy('bulan')
            ->values();

        return view('dashboard.penanggung-jawab', compact(
            'grafikPenjualan',
            'grafikPembelian'
        ));
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
