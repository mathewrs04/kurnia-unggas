<?php

namespace App\Http\Controllers;

use App\Jobs\TrainForecastJob;
use App\Models\BatchPembelian;
use App\Models\ForecastPenjualans;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ForecastController extends Controller
{
    public function train()
    {
        TrainForecastJob::dispatch();

        return response()->json([
            "status" => "ok",
            "message" => "Training started in background"
        ]);
    }

    public function generate(Request $req)
    {
        $days = (int) ($req->days ?? 30);

        try {
            $response = Http::timeout(60)->get('http://127.0.0.1:8000/predict', [
                'days' => $days
            ]);
        } catch (Exception $e) {
            return response()->json(["status" => "error", "message" => "Tidak dapat terhubung ke server AI: " . $e->getMessage()], 500);
        }

        if (!$response->successful()) {
            return response()->json(["status" => "error", "message" => "Server AI mengembalikan error."], 500);
        }

        $rows = $response->json('data');

        if (empty($rows)) {
            return response()->json(["status" => "error", "message" => "Data prediksi kosong."], 422);
        }

        DB::transaction(function () use ($rows) {

            ForecastPenjualans::query()->delete();

            ForecastPenjualans::insert(
                collect($rows)->map(fn($r) => [
                    "tanggal"    => $r["ds"],
                    "prediksi"   => round($r["yhat"]),
                    "lower"      => round($r["yhat_lower"]),
                    "upper"      => round($r["yhat_upper"]),
                    "created_at" => now(),
                    "updated_at" => now()
                ])->toArray()
            );
        });

        return response()->json(["status" => "ok", "count" => count($rows)]);
    }

    public function index()
    {
        $forecast = ForecastPenjualans::orderBy('tanggal', 'asc')->get();

        return view('forecast.index', compact('forecast'));
    }

    public function data(Request $request)
    {
        $query = ForecastPenjualans::orderBy('tanggal');

        if ($request->filled('start')) {
            $query->whereDate('tanggal', '>=', $request->start);
        }

        if ($request->filled('end')) {
            $query->whereDate('tanggal', '<=', $request->end);
        }

        $forecasts = $query->get();

        // Ambil data aktual (penjualan nyata) untuk periode yang sama
        $actualQuery = DB::table('penjualan_ayam_harians')
            ->select('tanggal', 'total_ekor as aktual')
            ->orderBy('tanggal');

        if ($request->filled('start')) {
            $actualQuery->whereDate('tanggal', '>=', $request->start);
        }

        if ($request->filled('end')) {
            $actualQuery->whereDate('tanggal', '<=', $request->end);
        }

        $actuals = $actualQuery->get()->keyBy('tanggal');

        return $forecasts->map(function ($f) use ($actuals) {
            $actual = $actuals->get($f->tanggal);
            return [
                'tanggal' => $f->tanggal,
                'prediksi' => $f->prediksi,
                'lower'    => $f->lower,
                'upper'    => $f->upper,
                'aktual'   => $actual ? (int) $actual->aktual : null,
            ];
        });
    }

    public function evaluate(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end'   => 'required|date|after_or_equal:start',
        ]);

        $start = $request->start;
        $end   = $request->end;

        // Ambil data prediksi untuk rentang tersebut
        $forecasts = ForecastPenjualans::whereBetween('tanggal', [$start, $end])
            ->orderBy('tanggal')
            ->get();

        if ($forecasts->isEmpty()) {
            return response()->json(['error' => 'Tidak ada data prediksi untuk rentang ini. Silakan generate terlebih dahulu.'], 404);
        }

        // Ambil data aktual dari penjualan_ayam_harians
        $actuals = DB::table('penjualan_ayam_harians')
            ->whereBetween('tanggal', [$start, $end])
            ->pluck('total_ekor', 'tanggal');

        $errors = [];
        $n = 0;
        $sumAbsError = 0;
        $sumSquaredError = 0;
        $sumAbsPercentError = 0;

        foreach ($forecasts as $f) {
            $tanggal = $f->tanggal;
            $prediksi = $f->prediksi;
            $aktual = $actuals->get($tanggal);

            if ($aktual !== null) {
                $error = $aktual - $prediksi;
                $absError = abs($error);
                $squaredError = $error * $error;
                $percentError = ($aktual != 0) ? ($absError / $aktual) * 100 : 0;

                $sumAbsError += $absError;
                $sumSquaredError += $squaredError;
                $sumAbsPercentError += $percentError;
                $n++;

                $errors[] = [
                    'tanggal' => $tanggal,
                    'aktual'  => $aktual,
                    'prediksi' => $prediksi,
                    'error'   => round($error, 2),
                    'abs_error' => round($absError, 2),
                    'percent_error' => round($percentError, 2),
                ];
            }
        }

        if ($n == 0) {
            return response()->json(['error' => 'Tidak ada data aktual untuk rentang ini.'], 404);
        }

        $mae = $sumAbsError / $n;
        $rmse = sqrt($sumSquaredError / $n);
        $mape = ($sumAbsPercentError / $n);

        return response()->json([
            'start' => $start,
            'end'   => $end,
            'total_data' => $n,
            'mae'   => round($mae, 2),
            'rmse'  => round($rmse, 2),
            'mape'  => round($mape, 2),
            'details' => $errors,
        ]);
    }

    public function rekomendasi(Request $request)
    {
        $request->validate([
            'mulai' => 'nullable|date',
            'sampai' => 'nullable|date|after_or_equal:mulai',
        ]);

        $mulai = $request->get('mulai', now()->toDateString());
        $sampai = $request->get('sampai', now()->addDays(7)->toDateString());

        $hariPrediksi = Carbon::parse($mulai)->diffInDays(Carbon::parse($sampai)) + 1;

        // Ambil prediksi dari database (pastikan sudah digenerate)
        $forecasts = ForecastPenjualans::whereBetween('tanggal', [$mulai, $sampai])
            ->orderBy('tanggal')
            ->get();

        $totalPrediksi = $forecasts->sum('prediksi');

        // Ambil semua batch yang stoknya > 0
        $batches = BatchPembelian::where('stok_ekor', '>', 0)
            ->orderBy('kode_batch')
            ->get();

        // Parameter bisnis
        $minimalStokAman = 50; // sisa di bawah 50 harus beli
        $rataRataBeli = 200;   // kebiasaan beli 200 ekor

        $rekomendasi = [];
        foreach ($batches as $batch) {
            $stok = $batch->stok_ekor;
            $minimalBatch = $batch->stok_ekor_minimal ?? $minimalStokAman;
            $sisaSetelahPrediksi = $stok - $totalPrediksi;

            $jumlahBeli = 0;
            $pesan = '';

            if ($sisaSetelahPrediksi < $minimalBatch) {
                $kebutuhan = $totalPrediksi - $stok + $minimalBatch;
                if ($kebutuhan > 0) {
                    $jumlahBeli = ceil($kebutuhan / $rataRataBeli) * $rataRataBeli;
                    $pesan = "Prediksi {$hariPrediksi} hari: {$totalPrediksi} ekor. Sisa stok diperkirakan {$sisaSetelahPrediksi} ekor (di bawah {$minimalBatch}). Rekomendasi beli {$jumlahBeli} ekor.";
                } else {
                    $pesan = "Stok aman, tidak perlu beli.";
                }
            } else {
                $pesan = "Stok mencukupi untuk {$hariPrediksi} hari ke depan.";
            }

            $rekomendasi[] = [
                'batch' => $batch,
                'stok' => $stok,
                'minimal' => $minimalBatch,
                'prediksi' => $totalPrediksi,
                'sisa' => $sisaSetelahPrediksi,
                'beli' => $jumlahBeli,
                'pesan' => $pesan,
            ];
        }

        return view('forecast.rekomendasi', compact('rekomendasi', 'mulai', 'sampai', 'totalPrediksi', 'hariPrediksi'));
    }
}
