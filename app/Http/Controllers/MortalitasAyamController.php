<?php

namespace App\Http\Controllers;

use App\Models\BatchPembelian;
use App\Models\MortalitasAyam;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MortalitasAyamController extends Controller
{
    public function index()
    {
        $mortalitas = MortalitasAyam::with('batch')->latest('tanggal_mati')->get();
        $batch = BatchPembelian::orderBy('kode_batch')->get();

        return view('mortalitas-ayam.index', compact('mortalitas', 'batch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'batch_pembelian_id' => 'required|exists:batch_pembelians,id',
            'tanggal_mati' => 'required|date',
            'jumlah_ekor' => 'required|integer|min:1',
            'berat_kg' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ],
        [
            'batch_pembelian_id.required' => 'Batch pembelian wajib dipilih.',
            'batch_pembelian_id.exists' => 'Batch pembelian yang dipilih tidak valid.',
            'tanggal_mati.required' => 'Tanggal mati wajib diisi.',
            'jumlah_ekor.required' => 'Jumlah ekor wajib diisi.',
            'berat_kg.required' => 'Berat (kg) wajib diisi.',
        ]);
       
        DB::beginTransaction();

        try {
            $batch = BatchPembelian::findOrFail($request->batch_pembelian_id);

            if ($batch->stok_ekor < $request->jumlah_ekor) {
                throw new Exception('Stok ekor pada batch pembelian tidak mencukupi.');
            }

            // Kurangi stok pada batch pembelian
            $batch->stok_ekor -= $request->jumlah_ekor;
            $batch->stok_kg -= $request->berat_kg;
            $batch->save();

            // Simpan data mortalitas ayam
            MortalitasAyam::create([
                'user_id' => auth()->id(),
                'batch_pembelian_id' => $request->batch_pembelian_id,
                'tanggal_mati' => $request->tanggal_mati,
                'jumlah_ekor' => $request->jumlah_ekor,
                'berat_kg' => $request->berat_kg,
                'catatan' => $request->catatan,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        toast()->success('Data mortalitas tersimpan');

        return redirect()->route('mortalitas-ayam.index');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $mortalitas = MortalitasAyam::findOrFail($id);
            $batch = BatchPembelian::findOrFail($mortalitas->batch_pembelian_id);
            $mortalitas->delete();
        });

        toast()->success('Data mortalitas dihapus');

        return redirect()->route('mortalitas-ayam.index');
    }



   

    

   
}
