<?php

namespace App\Http\Controllers;

use App\Models\BiayaOperasional;
use App\Models\PembelianDetail;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\StokOpname;
use App\Models\MortalitasAyam;
use App\Models\BatchPembelian;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanKeuntunganController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', now()->year);

        $bulanList = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $rows = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $totalPenjualan = Penjualan::whereYear('tanggal_jual', $tahun)
                ->whereMonth('tanggal_jual', $bulan)
                ->sum('subtotal');

            $totalHPP = PenjualanDetail::whereHas('penjualan', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_jual', $tahun)
                    ->whereMonth('tanggal_jual', $bulan);
            })->with('batch')->get()->sum(function ($detail) {
                $hargaBeli = $detail->batch ? $detail->batch->harga_beli_per_kg : 0;
                return $hargaBeli * $detail->jumlah_berat;
            });

            $totalPembelian = PembelianDetail::whereHas('pembelian', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_pembelian', $tahun)
                    ->whereMonth('tanggal_pembelian', $bulan);
            })->sum('subtotal');

            $totalBiayaOperasional = BiayaOperasional::whereYear('tanggal_biaya', $tahun)
                ->whereMonth('tanggal_biaya', $bulan)
                ->sum('subtotal');

            // 1. Rugi Susut DO
            $rugiSusutDO = PembelianDetail::whereHas('pembelian', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_pembelian', $tahun)->whereMonth('tanggal_pembelian', $bulan);
            })->get()->sum(function ($detail) {
                return ($detail->susut_kg ?? 0) * ($detail->harga_beli_per_kg ?? 0);
            });

            // 2. Rugi Susut Opname
            $rugiSusutOpname = StokOpname::whereYear('tanggal_opname', $tahun)
                ->whereMonth('tanggal_opname', $bulan)
                ->with('batch')->get()->sum(function ($opname) {
                    $hargaBeli = $opname->batch ? $opname->batch->harga_beli_per_kg : 0;
                    return max(0, $opname->susut_kg) * $hargaBeli;
                });

            // 3. Rugi Mortalitas
            $rugiMortalitas = MortalitasAyam::whereYear('tanggal_mati', $tahun)
                ->whereMonth('tanggal_mati', $bulan)
                ->with('batch')->get()->sum(function ($mortalitas) {
                    $hargaBeli = $mortalitas->batch ? $mortalitas->batch->harga_beli_per_kg : 0;
                    return ($mortalitas->berat_kg ?? 0) * $hargaBeli;
                });

            // 4. Rugi Susut Keseluruhan Sisa (Hanya untuk batch yang HABIS di bulan ini)
            $rugiSusutKeseluruhan = 0;
            $batchesHabis = BatchPembelian::where('stok_ekor', '<=', 0)
                ->with(['pembelianDetails.timbangan', 'penjualanDetails.penjualan', 'mortalitas', 'stokOpnames'])
                ->get();

            foreach ($batchesHabis as $batch) {
                // Cari kapan batch ini habis (ambil tanggal transaksi paling akhir)
                $lastJual = $batch->penjualanDetails->max(function ($d) {
                    return optional($d->penjualan)->tanggal_jual; });
                $lastMati = $batch->mortalitas->max('tanggal_mati');
                $lastOpname = $batch->stokOpnames->max('tanggal_opname');

                $maxDate = max($lastJual, $lastMati, $lastOpname);

                if ($maxDate) {
                    $date = Carbon::parse($maxDate);
                    // Jika batch ini benar-benar habis di bulan dan tahun iterasi ini
                    if ($date->year == $tahun && $date->month == $bulan) {
                        $detailAwal = $batch->pembelianDetails->sortBy('id')->first();
                        $stokAwalKg = (float) ($detailAwal?->timbangan?->total_berat ?? 0);
                        $totalBeratTerjual = (float) $batch->penjualanDetails->sum('jumlah_berat');
                        $totalBeratMortalitas = (float) $batch->mortalitas->sum('berat_kg');
                        $totalSusutOpname = (float) $batch->stokOpnames->sum('susut_kg');

                        // Total susut fisik keseluruhan
                        $susutFisik = $stokAwalKg - ($totalBeratTerjual + $totalBeratMortalitas);
                        // Susut yang belum pernah dicatat lewat opname
                        $sisaSusutBelumTercatat = $susutFisik - $totalSusutOpname;

                        if ($sisaSusutBelumTercatat > 0) {
                            $rugiSusutKeseluruhan += $sisaSusutBelumTercatat * ($batch->harga_beli_per_kg ?? 0);
                        }
                    }
                }
            }

            $labaKotor = $totalPenjualan - $totalHPP;
            
            $labaBersih = $labaKotor - $totalBiayaOperasional - $rugiSusutDO - $rugiSusutOpname - $rugiMortalitas - $rugiSusutKeseluruhan;

            $rows[$bulan] = [
                'nama_bulan' => $bulanList[$bulan],
                'total_penjualan' => $totalPenjualan,
                'total_pembelian' => $totalPembelian,
                'total_biaya_operasional' => $totalBiayaOperasional,
                'rugi_susut_do' => $rugiSusutDO,
                'rugi_susut_opname' => $rugiSusutOpname,
                'rugi_mortalitas' => $rugiMortalitas,
                'rugi_susut_keseluruhan' => $rugiSusutKeseluruhan,
                'laba_rugi' => $labaBersih,
            ];
        }

        $grandTotalPenjualan = collect($rows)->sum('total_penjualan');
        $grandTotalPembelian = collect($rows)->sum('total_pembelian');
        $grandTotalBiayaOperasional = collect($rows)->sum('total_biaya_operasional');
        $grandRugiSusutDO = collect($rows)->sum('rugi_susut_do');
        $grandRugiSusutOpname = collect($rows)->sum('rugi_susut_opname');
        $grandRugiMortalitas = collect($rows)->sum('rugi_mortalitas');
        $grandRugiSusutKeseluruhan = collect($rows)->sum('rugi_susut_keseluruhan');
        $grandLabaRugi = collect($rows)->sum('laba_rugi');

        // Rentang tahun untuk dropdown (5 tahun ke belakang s/d tahun ini)
        $tahunList = range(now()->year, now()->year - 5);

        return view('report.keuntungan', compact(
            'rows',
            'tahun',
            'tahunList',
            'grandTotalPenjualan',
            'grandTotalPembelian',
            'grandTotalBiayaOperasional',
            'grandRugiSusutDO',
            'grandRugiSusutOpname',
            'grandRugiMortalitas',
            'grandRugiSusutKeseluruhan',
            'grandLabaRugi'
        ));
    }
}
