<?php

namespace App\Http\Controllers;

use App\Jobs\TrainForecastJob;
use App\Models\ForecastPenjualans;
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
        } catch (\Exception $e) {
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
}
