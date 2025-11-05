<?php

namespace App\Http\Controllers;

use App\Models\BatchPembelian;
use Illuminate\Http\Request;

class BatchPembelianController extends Controller
{
    public function index()
    {
        $batchPembelians = BatchPembelian::all();
        return view('batch-pembelian.index', compact('batchPembelians'));
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $request->validate([
            // 'kode_batch' => 'required|unique:batch_pembelians,kode_batch,' . $id,
            'harga_beli_per_kg' => 'required|numeric|min:0',
            'stok_ekor' => 'required|integer|min:0',
            'stok_ekor_minimal' => 'required|integer|min:0',
            'stok_kg' => 'required|numeric|min:0',
        ], [
            // 'kode_batch.required' => 'Kode batch wajib diisi.',
            // 'kode_batch.unique' => 'Kode batch sudah digunakan.',
            'harga_beli_per_kg.required' => 'Harga beli per kg wajib diisi.',
            'harga_beli_per_kg.numeric' => 'Harga beli per kg harus berupa angka.',
            'harga_beli_per_kg.min' => 'Harga beli per kg tidak boleh kurang dari 0.',
            'stok_ekor.required' => 'Stok ekor wajib diisi.',
            'stok_ekor.integer' => 'Stok ekor harus berupa bilangan bulat.',
            'stok_ekor.min' => 'Stok ekor tidak boleh kurang dari 0.',
            'stok_ekor_minimal.required' => 'Stok ekor minimal wajib diisi.',
            'stok_ekor_minimal.integer' => 'Stok ekor minimal harus berupa bilangan bulat.',
            'stok_ekor_minimal.min' => 'Stok ekor minimal tidak boleh kurang dari 0.',
            'stok_kg.required' => 'Stok kg wajib diisi.',
            'stok_kg.numeric' => 'Stok kg harus berupa angka.',
            'stok_kg.min' => 'Stok kg tidak boleh kurang dari 0.',
        ]);

        $newRequest = [
            'id' => $id,
            'harga_beli_per_kg' => $request->harga_beli_per_kg,
            'stok_ekor' => $request->stok_ekor,
            'stok_ekor_minimal' => $request->stok_ekor_minimal,
            'stok_kg' => $request->stok_kg,
        ];

        if (!$id) {
            $newRequest['kode_batch'] = BatchPembelian::kodeBatch();
        }

        BatchPembelian::updateOrCreate(
            ['id' => $id],
            $newRequest
        );

        toast()->success('Data berhasil disimpan!');

        return redirect()->route('master.batch-pembelian.index');
    }
}
