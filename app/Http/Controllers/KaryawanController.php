<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawans = Karyawan::all();
        confirmDelete('HapusData', 'Yakin hapus data ini?');
        return view('karyawan.index', compact('karyawans'));
    }

    public function store()
    {
        $id = request()->id;
        request()->validate([
            'nama'   => 'required|string|unique:karyawans,nama,' . $id,
            'posisi' => 'required|string|max:100',
        ], [
            'nama.required'   => 'Nama karyawan wajib diisi.',
            'nama.unique'     => 'Nama karyawan sudah digunakan.',
            'nama.string'     => 'Nama karyawan harus berupa teks.',
            'posisi.required' => 'Posisi karyawan wajib diisi.',
            'posisi.string'   => 'Posisi karyawan harus berupa teks.',
            'posisi.max'      => 'Posisi karyawan tidak boleh lebih dari 100 karakter.',
        ]);

        Karyawan::updateOrCreate(
            ['id' => $id],
            [
                'nama'    => request()->nama,
                'posisi'  => request()->posisi,
                'user_id' => $id ? Karyawan::find($id)->user_id : auth()->id(),
            ]
        );

        toast()->success('Data berhasil disimpan!');
        return redirect()->route('master.karyawan.index');
    }

    public function destroy(string $id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();
        toast()->success('Data berhasil dihapus!');
        return redirect()->route('master.karyawan.index');
    }
}
