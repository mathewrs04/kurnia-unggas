<?php

namespace App\Http\Controllers;

use App\Models\MetodePembayaran;
use Illuminate\Http\Request;

class MetodePembayaranController extends Controller
{
    public function index()
    {
        $metodePembayaran = MetodePembayaran::orderBy('nama_metode')->get();
        confirmDelete('HapusData', 'Yakin hapus data ini?');
        return view('metode-pembayaran.index', compact('metodePembayaran'));
    }

    public function store(Request $request)
    {
        $id = $request->id;
        $validated = $request->validate([
            'nama_metode' => 'required|string|max:100|unique:metode_pembayarans,nama_metode,' . $id,
            'keterangan' => 'nullable|string|max:255',
        ], [
            'nama_metode.required' => 'Nama metode wajib diisi.',
            'nama_metode.string' => 'Nama metode harus berupa teks.',
            'nama_metode.max' => 'Nama metode tidak boleh lebih dari 100 karakter.',
            'nama_metode.unique' => 'Nama metode sudah digunakan.',
            'keterangan.string' => 'Keterangan harus berupa teks.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 255 karakter.',
        ]);

        MetodePembayaran::updateOrCreate(
            ['id' => $id],
            [
                'nama_metode' => $validated['nama_metode'],
                'keterangan' => $validated['keterangan'] ?? null,
                'user_id' => $id ? MetodePembayaran::find($id)->user_id : auth()->id(),
            ]
        );

        toast()->success('Data berhasil disimpan!');
        return redirect()->route('master.metode-pembayaran.index');
    }

    public function destroy(string $id)
    {
        $metode = MetodePembayaran::findOrFail($id);
        $metode->delete();
        toast()->success('Data berhasil dihapus!');
        return redirect()->route('master.metode-pembayaran.index');
    }
}
