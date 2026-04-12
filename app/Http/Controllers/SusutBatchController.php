<?php

namespace App\Http\Controllers;

use App\Models\BatchPembelian;

class SusutBatchController extends Controller
{
    public function index()
    {
        $batches = BatchPembelian::with(['stokOpnames', 'mortalitas'])
            ->where('stok_ekor', '<=', 0)
            ->orderBy('kode_batch')
            ->get();

        return view('susut-batch.index', compact('batches'));
    }
}
