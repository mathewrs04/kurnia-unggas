<?php

namespace App\Http\Controllers;

use App\Models\HargaAyam;
use Illuminate\Http\Request;

class HargaAyamController extends Controller
{
    public function index()
    {
        $hargaAyams = HargaAyam::with('produk')
            ->orderByDesc('tanggal')
            ->get();

        confirmDelete('HapusData', 'Yakin hapus data ini?');

        return view('harga-ayam.index', compact('hargaAyams'));
    }

    public function store(Request $request)
    {
        $id = $request->id;

        $request->validate([
            'produks_id'   => 'required|exists:produks,id',
            'tanggal'      => 'required|date',
            'harga_eceran' => 'required|integer|min:0',
            'harga_partai' => 'required|integer|min:0',
        ], [
            'produks_id.required'   => 'Produk wajib dipilih.',
            'produks_id.exists'     => 'Produk tidak valid.',
            'tanggal.required'      => 'Tanggal wajib diisi.',
            'harga_eceran.required' => 'Harga eceran wajib diisi.',
            'harga_eceran.min'      => 'Harga eceran tidak boleh negatif.',
            'harga_partai.required' => 'Harga partai wajib diisi.',
            'harga_partai.min'      => 'Harga partai tidak boleh negatif.',
        ]);

        HargaAyam::updateOrCreate(
            ['id' => $id],
            [
                'produks_id'   => $request->produks_id,
                'tanggal'      => $request->tanggal,
                'harga_eceran' => $request->harga_eceran,
                'harga_partai' => $request->harga_partai,
                'user_id'      => $id ? HargaAyam::find($id)->user_id : auth()->id(),
            ]
        );

        toast()->success('Data harga berhasil disimpan!');
        return redirect()->route('master.harga-ayam.index');
    }

    public function destroy(string $id)
    {
        HargaAyam::findOrFail($id)->delete();
        toast()->success('Data harga berhasil dihapus!');
        return redirect()->route('master.harga-ayam.index');
    }
}
