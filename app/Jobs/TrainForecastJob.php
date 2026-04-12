<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TrainForecastJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
         $sales = DB::table('penjualan_ayam_harians')
            ->select('tanggal as ds', 'total_ekor as y')
            ->whereYear('tanggal', '>=', 2022)
            ->whereYear('tanggal', '<=', 2024)
            ->orderBy('tanggal')
            ->get();

        $holidays = DB::table('holidays')->get()->map(function ($h) {
            return [
                "holiday"   => $h->name,
                "ds"        => $h->date,
                "pre_days"  => $h->pre_days,
                "post_days" => $h->post_days,
            ];
        })->values();

        Http::timeout(300)->post("http://127.0.0.1:8000/train", [
            "training_data" => $sales->toArray(),
            "holiday_data"  => $holidays->toArray(),
        ]);
    }
}
