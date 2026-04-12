<?php

namespace App\Http\Controllers;

use App\Models\BiayaOperasional;
use App\Models\MetodePembayaran;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class BiayaOperasionalController extends Controller
{
    public function index()
    {
        $biayaOperasionals = BiayaOperasional::with(['produk', 'metodePembayaran'])
            ->latest()
            ->get();

        return view('biaya-operasional.index', compact('biayaOperasionals'));
    }

    public function create()
    {
        $noNota = BiayaOperasional::generateNoNota();
        $produks = Produk::orderBy('nama_produk')->get();
        $metodes = MetodePembayaran::orderBy('nama_metode')->get();

        return view('biaya-operasional.create', compact('noNota', 'produks', 'metodes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_nota' => ['required', 'string', 'unique:biaya_operasionals,no_nota'],
            'produk_id' => ['required', 'exists:produks,id'],
            'metode_pembayaran_id' => ['required', 'exists:metode_pembayarans,id'],
            'tanggal_biaya' => ['required', 'date'],
            'foto_nota' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'harga_satuan' => ['required', 'integer', 'min:0'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $subtotal = $validated['harga_satuan'] * $validated['jumlah'];
        $path = $request->file('foto_nota')->store('biaya-operasional', 'public');

        BiayaOperasional::create([
            'no_nota' => $validated['no_nota'],
            'produk_id' => $validated['produk_id'],
            'metode_pembayaran_id' => $validated['metode_pembayaran_id'],
            'tanggal_biaya' => $validated['tanggal_biaya'],
            'foto_nota' => $path,
            'harga_satuan' => $validated['harga_satuan'],
            'jumlah' => $validated['jumlah'],
            'subtotal' => $subtotal,
            'user_id' => auth()->id(),
        ]);

        Alert::success('Berhasil', 'Biaya operasional berhasil disimpan');
        return redirect()->route('biaya-operasional.index');
    }

    public function show($id)
    {
        $biaya = BiayaOperasional::with(['produk', 'metodePembayaran'])->findOrFail($id);

        return view('biaya-operasional.show', compact('biaya'));
    }

    public function edit($id)
    {
        $biaya = BiayaOperasional::findOrFail($id);
        $produks = Produk::orderBy('nama_produk')->get();
        $metodes = MetodePembayaran::orderBy('nama_metode')->get();

        return view('biaya-operasional.edit', compact('biaya', 'produks', 'metodes'));
    }

    public function update(Request $request, $id)
    {
        $biaya = BiayaOperasional::findOrFail($id);

        $validated = $request->validate([
            'no_nota' => [
                'required',
                'string',
                Rule::unique('biaya_operasionals', 'no_nota')->ignore($biaya->id),
            ],
            'produk_id' => ['required', 'exists:produks,id'],
            'metode_pembayaran_id' => ['required', 'exists:metode_pembayarans,id'],
            'tanggal_biaya' => ['required', 'date'],
            'foto_nota' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'harga_satuan' => ['required', 'integer', 'min:0'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $data = [
            'no_nota' => $validated['no_nota'],
            'produk_id' => $validated['produk_id'],
            'metode_pembayaran_id' => $validated['metode_pembayaran_id'],
            'tanggal_biaya' => $validated['tanggal_biaya'],
            'harga_satuan' => $validated['harga_satuan'],
            'jumlah' => $validated['jumlah'],
            'subtotal' => $validated['harga_satuan'] * $validated['jumlah'],
        ];

        if ($request->hasFile('foto_nota')) {
            if ($biaya->foto_nota) {
                Storage::disk('public')->delete($biaya->foto_nota);
            }
            $data['foto_nota'] = $request->file('foto_nota')->store('biaya-operasional', 'public');
        }

        $biaya->update($data);

        Alert::success('Berhasil', 'Biaya operasional berhasil diupdate');
        return redirect()->route('biaya-operasional.index');
    }

    public function destroy($id)
    {
        $biaya = BiayaOperasional::findOrFail($id);

        if ($biaya->foto_nota) {
            Storage::disk('public')->delete($biaya->foto_nota);
        }

        $biaya->delete();

        Alert::success('Berhasil', 'Biaya operasional berhasil dihapus');
        return redirect()->route('biaya-operasional.index');
    }
}
