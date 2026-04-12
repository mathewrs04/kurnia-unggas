<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = Produk::all();
        confirmDelete('HapusData', 'Yakin hapus data ini?');
        return view('produk.index', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'tipe_produk' => 'required|in:ayam_hidup,jasa,barang_operasional,biaya_operasional',
            'satuan' => 'required|string|max:100',
            'harga_satuan' => 'required|numeric',
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'tipe_produk.required' => 'Tipe produk wajib dipilih.',
            'tipe_produk.in' => 'Tipe produk tidak valid.',
            'satuan.required' => 'Satuan wajib diisi.',
            'harga_satuan.required' => 'Harga satuan wajib diisi.',
            'harga_satuan.numeric' => 'Harga satuan harus berupa angka.',
        ]);

        Produk::updateOrCreate(
            ['id' => $request->id],
            [
                'nama_produk' => $request->nama_produk,
                'tipe_produk' => $request->tipe_produk,
                'satuan' => $request->satuan,
                'harga_satuan' => $request->harga_satuan,
                'user_id' => $request->id ? Produk::find($request->id)->user_id : auth()->id(),
            ]
        );

        toast()->success('Data berhasil disimpan!');
        return redirect()->route('master.produk.index');
    }

    public function destroy(string $id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();
        toast()->success('Data berhasil dihapus!');
        return redirect()->route('master.produk.index');
    }
}
