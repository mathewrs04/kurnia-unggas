<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{

    public function index()
    {
        $pelanggans = Pelanggan::all();
        confirmDelete('HapusData', 'Yakin hapus data ini?');
        return view('pelanggan.index', compact('pelanggans'));
    }

    public function store(Request $request)
    {
        $id = $request->id;

        $request->validate([
            'nama' => 'required|string|max:100|unique:pelanggans,nama,' . $id,
            'alamat' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
        ], [
            'nama.required' => 'Nama pelanggan wajib diisi.',
            'nama.unique' => 'Nama pelanggan sudah digunakan.',
            'nama.string' => 'Nama pelanggan harus berupa teks.',
            'nama.max' => 'Nama pelanggan tidak boleh lebih dari 100 karakter.',
            'alamat.required' => 'Alamat pelanggan wajib diisi.',
            'alamat.string' => 'Alamat pelanggan harus berupa teks.',
            'alamat.max' => 'Alamat pelanggan tidak boleh lebih dari 255 karakter.',
            'no_telp.required' => 'No. Telp pelanggan wajib diisi.',
            'no_telp.string' => 'No. Telp pelanggan harus berupa teks.',
            'no_telp.max' => 'No. Telp pelanggan tidak boleh lebih dari 20 karakter.',
        ]);

        Pelanggan::updateOrCreate(
            ['id' => $id],
            [
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'no_telp' => $request->no_telp,
                'user_id' => $id ? Pelanggan::find($id)->user_id : auth()->id(),
            ]
        );

        toast()->success('Data berhasil disimpan!');
        return redirect()->route('master.pelanggan.index');
    }

    public function destroy(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();

        toast()->success('Data berhasil dihapus!');
        return redirect()->route('master.pelanggan.index');
    }

    public function laporan()
    {
        $pelanggans = Pelanggan::withCount('penjualans')
            ->withSum('penjualans', 'subtotal')
            ->orderByDesc('penjualans_sum_subtotal')
            ->orderByDesc('penjualans_count')
            ->orderBy('nama')
            ->get();

        return view('report.pelanggan', compact('pelanggans'));
    }
}
