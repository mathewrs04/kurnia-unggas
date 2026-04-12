<?php

namespace App\Http\Controllers;

use App\Models\BatchPembelian;
use App\Models\Karyawan;
use App\Models\Keranjang;
use App\Models\StokOpname;
use App\Models\Timbangan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokOpnameController extends Controller
{
    public function index()
    {
        $stokOpnames = StokOpname::with(['batch', 'timbangan'])->latest('tanggal_opname')->get();
        return view('stok-opname.index', compact('stokOpnames'));
    }

    public function create()
    {
        $batches = BatchPembelian::orderBy('kode_batch')->get();
        $karyawans = Karyawan::orderBy('nama')->get();

        return view('stok-opname.create', compact('batches', 'karyawans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'batch_pembelian_id' => 'required|exists:batch_pembelians,id',
            'tanggal_opname' => 'required|date',
            'keranjangs' => 'required|array|min:1',
            'keranjangs.*.jumlah_ekor' => 'required|integer|min:1',
            'keranjangs.*.berat_keranjang' => 'required|numeric|min:0',
            'keranjangs.*.berat_total' => 'required|numeric|min:0',
            'keranjangs.*.berat_ayam' => 'required|numeric|min:0',
            'jumlah_berat_aktual' => 'nullable|numeric|min:0',
            'jumlah_ekor'         => 'nullable|integer|min:0',
            'karyawan_ids'        => 'nullable|array',
            'karyawan_ids.*'      => 'integer|exists:karyawans,id',
        ],
        [
            'batch_pembelian_id.required'            => 'Batch pembelian wajib dipilih.',
            'batch_pembelian_id.exists'              => 'Batch pembelian yang dipilih tidak valid.',
            'tanggal_opname.required'                => 'Tanggal opname wajib diisi.',
            'keranjangs.required'                    => 'Keranjang tidak boleh kosong.',
            'keranjangs.*.jumlah_ekor.required'      => 'Jumlah ekor pada keranjang :index wajib diisi.',
            'keranjangs.*.berat_keranjang.required'  => 'Berat keranjang pada keranjang :index wajib diisi.',
            'keranjangs.*.berat_total.required'      => 'Berat total pada keranjang :index wajib diisi.',
            'keranjangs.*.berat_ayam.required'       => 'Berat ayam pada keranjang :index wajib diisi.',
        ]);

       


        DB::beginTransaction();

        try {
            $batch = BatchPembelian::findOrFail($data['batch_pembelian_id']);

           

            // 1. Hitung total jumlah ekor dan total berat dari keranjang
            $totalJumlahEkor = 0;
            $totalBerat = 0;

            foreach ($request->keranjangs as $keranjang) {
                $totalJumlahEkor += $keranjang['jumlah_ekor'];
                $totalBerat += $keranjang['berat_ayam'];
            }

            if ($totalBerat != $data['jumlah_berat_aktual']) {
                throw new Exception('Jumlah berat aktual tidak sesuai dengan total berat dari keranjang.');
            }

            if ($batch->stok_ekor != $totalJumlahEkor) {
                throw new Exception('Jumlah ekor aktual tidak sesuai dengan stok ekor pada batch pembelian.');
            }

            $timbangan = Timbangan::create([
                'jenis'             => 'timbangan_stok_opname',
                'tanggal'           => $request->tanggal_opname,
                'total_jumlah_ekor' => $totalJumlahEkor,
                'total_berat'       => $request->jumlah_berat_aktual,
            ]);
            $timbangan->karyawans()->sync($request->karyawan_ids ?? []);

            // 3. Simpan data keranjang
            foreach ($request->keranjangs as $keranjangData) {
                Keranjang::create([
                    'timbangan_id' => $timbangan->id,
                    'jumlah_ekor' => $keranjangData['jumlah_ekor'],
                    'berat_keranjang' => $keranjangData['berat_keranjang'],
                    'berat_total' => $keranjangData['berat_total'],
                    'berat_ayam' => $keranjangData['berat_ayam'],
                ]);
            }

            $susut = $batch->stok_kg - $totalBerat;

            $stokOpname = StokOpname::create([
                'user_id' => auth()->id(),
                'batch_pembelian_id' => $batch->id,
                'timbangan_id' => $timbangan->id,
                'tanggal_opname' => $data['tanggal_opname'],
                'stok_ekor_sistem' => $batch->stok_ekor,
                'stok_kg_sistem' => $batch->stok_kg,
                'berat_aktual_kg' => $totalBerat,
                'susut_kg' => $susut,
            ]);

            BatchPembelian::where('id', $batch->id)
                ->update([
                    'stok_kg' => $totalBerat,
                ]);


            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan stok opname: ' . $e->getMessage()])->withInput();
        }

        toast()->success('Stok opname berhasil dicatat');

        return redirect()->route('stok-opname.index');
    }

    public function show($id)
    {
        $stokOpname = StokOpname::with(['batch', 'timbangan'])->findOrFail($id);

        return view('stok-opname.show', compact('stokOpname'));
    }

    public function edit($id)
    {
        $stokOpname = StokOpname::findOrFail($id);
        $batches = BatchPembelian::orderBy('kode_batch')->get();
        $timbangans = Timbangan::orderBy('tanggal', 'desc')->get();
        $karyawans = Karyawan::orderBy('nama')->get();

        return view('stok-opname.edit', compact('stokOpname', 'batches', 'timbangans', 'karyawans'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'batch_pembelian_id' => 'required|exists:batch_pembelians,id',
            'tanggal_opname' => 'required|date',
            'keranjangs' => 'required|array|min:1',
            'keranjangs.*.jumlah_ekor' => 'required|integer|min:1',
            'keranjangs.*.berat_keranjang' => 'required|numeric|min:0',
            'keranjangs.*.berat_total' => 'required|numeric|min:0',
            'keranjangs.*.berat_ayam' => 'required|numeric|min:0',
            'jumlah_berat_aktual' => 'nullable|numeric|min:0',
            'jumlah_ekor'         => 'nullable|integer|min:0',
            'karyawan_ids'        => 'nullable|array',
            'karyawan_ids.*'      => 'integer|exists:karyawans,id',
        ]);

        DB::transaction(function () use ($id, $data) {
            $stokOpname = StokOpname::findOrFail($id);
            $batch = BatchPembelian::findOrFail($data['batch_pembelian_id']);

            $totalBerat = 0;
            foreach ($data['keranjangs'] as $keranjang) {
                $totalBerat += $keranjang['berat_ayam'];
            }

            $susut = $batch->stok_kg - $totalBerat;

            $stokOpname->update([
                'batch_pembelian_id' => $batch->id,
                'tanggal_opname' => $data['tanggal_opname'],
                'stok_kg_sistem' => $batch->stok_kg,
                'berat_aktual_kg' => $totalBerat,
                'susut_kg' => $susut,
            ]);
        });

        toast()->success('Stok opname diperbarui');

        return redirect()->route('stok-opname.index');
    }

    public function destroy($id)
    {
        $stokOpname = StokOpname::findOrFail($id);
        $stokOpname->delete();

        toast()->success('Stok opname dihapus');

        return redirect()->route('stok-opname.index');
    }

    

   
}
