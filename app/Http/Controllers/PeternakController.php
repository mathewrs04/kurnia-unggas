<?php

namespace App\Http\Controllers;

use App\Models\Peternak;

class PeternakController extends Controller
{
    public function index()
    {
        $peternaks = Peternak::with('pemasok')->get();
        confirmDelete('HapusData', 'Yakin hapus data ini?');
        return view('peternak.index', compact('peternaks'));
    }

    public function store()
    {
        $id = request()->id;
        request()->validate([
            'pemasok_id' => 'required|exists:pemasoks,id',
            'nama' => 'required|string|unique:peternaks,nama,'.$id,
            'alamat' => 'required|string|max:255',
            'no_telp' => 'required|string|max:15',
        ], [
            'pemasok_id.required' => 'Pemasok wajib diisi.',
            'pemasok_id.exists' => 'Pemasok tidak ditemukan.',
            'nama.required' => 'Nama peternak wajib diisi.',
            'nama.unique' => 'Nama peternak sudah digunakan.',
            'nama.string' => 'Nama peternak harus berupa teks.',
            'alamat.required' => 'Alamat peternak wajib diisi.',
            'alamat.string' => 'Alamat peternak harus berupa teks.',
            'alamat.max' => 'Alamat peternak tidak boleh lebih dari 255 karakter.',
            'no_telp.string' => 'No. Telp peternak harus berupa teks.',
            'no_telp.max' => 'No. Telp peternak tidak boleh lebih dari 15 karakter.',
            'no_telp.required' => 'No. Telp peternak wajib diisi.',
        ]);

        Peternak::updateOrCreate(
            ['id' => $id],
            [
                'pemasok_id' => request()->pemasok_id,
                'nama' => request()->nama,
                'alamat' => request()->alamat,
                'no_telp' => request()->no_telp,
                'user_id' => $id ? Peternak::find($id)->user_id : auth()->id(),
            ]
        );

        toast()->success('Data berhasil disimpan!');
        return redirect()->route('master.peternak.index');
    }

    public function destroy(string $id)
    {
        $peternak = Peternak::findOrFail($id);
        $peternak->delete();
        toast()->success('Data berhasil dihapus!');
        return redirect()->route('master.peternak.index');
    }

    public function getData()
    {
        $search = request()->query('search');
        $query = Peternak::query();
        $peternak = $query->where('nama', 'like', '%'.$search.'%')->get();
        return response()->json($peternak);
    }
}
