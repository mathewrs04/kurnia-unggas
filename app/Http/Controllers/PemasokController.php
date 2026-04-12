<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemasok;

class PemasokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pemasok = Pemasok::all();
        confirmDelete('HapusData', 'Yakin hapus data ini?');
        return view('pemasok.index', compact('pemasok'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $id = $request->id;
        $request->validate([
            'nama_pabrik' => 'required|string|max:255|unique:pemasoks,nama_pabrik,'.$id,
            'nama_marketing' => 'required|string|max:255',
            'no_telp_marketing' => 'required|string|max:45',
        ],
        [
            'nama_pabrik.required' => 'Nama pabrik wajib diisi.',
            'nama_pabrik.unique' => 'Nama pabrik sudah ada.',
            'nama_marketing.required' => 'Nama marketing wajib diisi.',
            'no_telp_marketing.required' => 'No. telp marketing wajib diisi.',
        ]);

        Pemasok::updateOrCreate(
            ['id' => $id],
            [
                'nama_pabrik' => $request->nama_pabrik,
                'nama_marketing' => $request->nama_marketing,
                'no_telp_marketing' => $request->no_telp_marketing,
                'user_id' => $id ? Pemasok::find($id)->user_id : auth()->id(),
            ]
        );

        toast()->success('Data berhasil disimpan!');
        return redirect()->route('master.pemasok.index');
    }

   
    public function destroy(string $id)
    {
        $pemasok = Pemasok::findOrFail($id);
        $pemasok->delete();
        toast()->success('Data berhasil dihapus!');
        return redirect()->route('master.pemasok.index');
    }
}
