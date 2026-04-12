<?php

namespace App\Http\Controllers;

use App\Models\BatchPembelian;
use App\Models\HargaAyam;
use App\Models\Karyawan;
use App\Models\Keranjang;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Timbangan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualans = Penjualan::with(['pelanggan', 'penjualanDetails.produk'])
            ->latest()
            ->get();

        return view('penjualan.index', compact('penjualans'));
    }

    public function create()
    {

        // Generate nomor nota otomatis
        $noNota = Penjualan::generateNoNota();

        $pelanggans = Pelanggan::orderBy('nama')->get();
        $produks = Produk::orderBy('nama_produk')->get();
        $batches = BatchPembelian::where('stok_ekor', '>', 0)->orderBy('kode_batch')->get();
        $timbangans = Timbangan::orderBy('tanggal', 'desc')->get();
        $hargaAyams = HargaAyam::orderBy('id')->get()->keyBy('tanggal');
        $karyawans = Karyawan::orderBy('nama')->get();

        return view('penjualan.create', compact('noNota', 'pelanggans', 'produks', 'batches', 'timbangans', 'hargaAyams', 'karyawans'));
    }

    public function store(Request $request)
    {


        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'tanggal_jual' => 'required|date',
            'no_nota' => 'required|string|unique:penjualans,no_nota',
            'diskon' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',

            // Validasi timbangan
            'karyawan_ids'    => 'nullable|array',
            'karyawan_ids.*'  => 'integer|exists:karyawans,id',

            // Validasi ayam
            'ayam.tipe_penjualan' => 'nullable|in:eceran,partai',
            'ayam.batch_id' => 'nullable|exists:batch_pembelians,id',
            'ayam.timbangan_id' => 'nullable|exists:timbangans,id',
            'ayam.jumlah_ekor' => 'nullable|integer|min:1',
            'ayam.jumlah_berat' => 'nullable|numeric|min:0',
            // Wajib diisi kalau ada penjualan ayam (eceran/partai)
            'ayam.harga_per_kg' => 'required_if:ayam.tipe_penjualan,eceran,partai|nullable|numeric|min:0',
            'ayam.keranjangs' => 'nullable|array',
            'ayam.keranjangs.*.jumlah_ekor' => 'nullable|integer|min:1',
            'ayam.keranjangs.*.berat_keranjang' => 'nullable|numeric|min:0',
            'ayam.keranjangs.*.berat_total' => 'nullable|numeric|min:0',
            'ayam.keranjangs.*.berat_ayam' => 'nullable|numeric|min:0',

            // Validasi jasa
            'jasa' => 'nullable|array',
            'jasa.*.produk_id' => 'nullable|exists:produks,id',
            'jasa.*.jumlah_ekor' => 'nullable|integer|min:1',

        ], [
            'pelanggan_id.required' => 'Pelanggan harus dipilih',
            'tanggal_jual.required' => 'Tanggal jual harus diisi',
            'no_nota.required' => 'Nomor nota harus diisi',
            'no_nota.unique' => 'Nomor nota sudah digunakan',
            'subtotal.required' => 'Subtotal harus diisi',
            'ayam.harga_per_kg.required_if' => 'Harga per kg harus diisi ketika ada penjualan ayam',
            'jasa.*.produk_id.exists' => 'Produk jasa tidak ditemukan',
            'jasa.*.jumlah_ekor.integer' => 'Jumlah ekor untuk jasa harus berupa angka',
            'jasa.*.jumlah_ekor.min' => 'Jumlah ekor untuk jasa harus minimal 1',
            'ayam.tipe_penjualan.in' => 'Tipe penjualan ayam harus eceran atau partai',
            'ayam.batch_id.exists' => 'Batch pembelian tidak ditemukan',
            'ayam.timbangan_id.exists' => 'Timbangan tidak ditemukan',
            'ayam.jumlah_ekor.integer' => 'Jumlah ekor untuk ayam harus berupa angka',
            'ayam.jumlah_ekor.min' => 'Jumlah ekor untuk ayam harus minimal 1',
            'ayam.jumlah_berat.numeric' => 'Jumlah berat untuk ayam harus berupa angka',
            'ayam.jumlah_berat.min' => 'Jumlah berat untuk ayam harus minimal 0',
            'ayam.keranjangs.*.jumlah_ekor.integer' => 'Jumlah ekor dalam keranjang harus berupa angka',
            'ayam.keranjangs.*.jumlah_ekor.min' => 'Jumlah ekor dalam keranjang harus minimal 1',
            'ayam.keranjangs.*.berat_keranjang.numeric' => 'Berat keranjang harus berupa angka',
            'ayam.keranjangs.*.berat_keranjang.min' => 'Berat keranjang harus minimal 0',
            'ayam.keranjangs.*.berat_total.numeric' => 'Berat total harus berupa angka',
            'ayam.keranjangs.*.berat_total.min' => 'Berat total harus minimal 0',
            'ayam.keranjangs.*.berat_ayam.numeric' => 'Berat ayam harus berupa angka',
            'ayam.keranjangs.*.berat_ayam.min' => 'Berat ayam harus minimal 0',

        ]);

        DB::beginTransaction();

        try {
            $subtotalSebelumDiskon = 0;
            $details = [];

            // Proses data ayam jika ada
            if ($request->has('ayam') && $request->ayam['tipe_penjualan']) {
                $ayam = $request->ayam;
                $produkAyam = Produk::where('tipe_produk', 'ayam_hidup')->first();

                if (!$produkAyam) {
                    throw new Exception('Produk ayam hidup tidak ditemukan');
                }

                $jumlahEkor = 0;
                $jumlahBerat = 0;

                if ($ayam['tipe_penjualan'] == 'eceran') {
                    // Eceran: langsung dari input
                    $jumlahEkor = $ayam['jumlah_ekor'];
                    $jumlahBerat = $ayam['jumlah_berat'];
                } else {
                    // Partai: hitung dari keranjang
                    if (isset($ayam['keranjangs']) && is_array($ayam['keranjangs'])) {
                        foreach ($ayam['keranjangs'] as $keranjang) {
                            $jumlahEkor += $keranjang['jumlah_ekor'];
                            $jumlahBerat += $keranjang['berat_ayam'];
                        }
                    }

                    // Buat data timbangan
                    $timbangan = Timbangan::create([
                        'jenis'             => 'timbangan_data_penjualan',
                        'tanggal'           => $request->tanggal_jual,
                        'total_jumlah_ekor' => $jumlahEkor,
                        'total_berat'       => $jumlahBerat,
                    ]);
                    $timbangan->karyawans()->sync($request->karyawan_ids ?? []);

                    // Simpan data keranjang
                    if (isset($ayam['keranjangs']) && is_array($ayam['keranjangs'])) {
                        foreach ($ayam['keranjangs'] as $keranjangData) {
                            Keranjang::create([
                                'timbangan_id'    => $timbangan->id,
                                'jumlah_ekor'     => $keranjangData['jumlah_ekor'],
                                'berat_keranjang' => $keranjangData['berat_keranjang'],
                                'berat_total'     => $keranjangData['berat_total'],
                                'berat_ayam'      => $keranjangData['berat_ayam'],
                            ]);
                        }
                    }
                }

                $hargaPerKg = $ayam['harga_per_kg'];

                // Pastikan harga per kg diisi ketika ada penjualan ayam
                if ($hargaPerKg === null || $hargaPerKg === '') {
                    throw new Exception('Harga per kg harus diisi untuk penjualan ayam.');
                }

                $subtotalSebelumDiskon = $jumlahBerat * $hargaPerKg;
                $subtotalAyam = $subtotalSebelumDiskon - $request->diskon;

                $batchId = $ayam['batch_id'] ?? null;
                $timbanganId = isset($timbangan) ? $timbangan->id : ($ayam['timbangan_id'] ?? null);

                $details[] = [
                    'produk_id' => $produkAyam->id,
                    'batch_id' => $batchId,
                    'timbangan_id' => $timbanganId,
                    'jumlah_ekor' => $jumlahEkor,
                    'jumlah_berat' => $jumlahBerat,
                    'harga_satuan' => $hargaPerKg,
                    'subtotal' => $subtotalAyam,
                ];
            }

            // Proses data jasa jika ada
            if ($request->has('jasa') && is_array($request->jasa)) {

                foreach ($request->jasa as $jasaItem) {
                    $produkJasa = Produk::find($jasaItem['produk_id']);

                    if (!$produkJasa) {
                        continue;
                    }

                    $jumlahEkor = $jasaItem['jumlah_ekor'];
                    $hargaSatuan = $produkJasa->harga_satuan;
                    $subtotalJasa = $jumlahEkor * $hargaSatuan;
                    $subtotalSebelumDiskon += $subtotalJasa;

                    $details[] = [
                        'produk_id' => $produkJasa->id,
                        'batch_id' => null,
                        'timbangan_id' => null,
                        'jumlah_ekor' => $jumlahEkor,
                        'jumlah_berat' => null,
                        'harga_satuan' => $hargaSatuan,
                        'subtotal' => $subtotalJasa,
                    ];
                }
            }

            // Validasi minimal 1 item
            if (empty($details)) {
                throw new Exception('Minimal harus ada 1 item penjualan (ayam atau jasa)');
            }


            // Buat data penjualan
            $penjualan = Penjualan::create([
                'no_nota' => $request->no_nota,
                'tanggal_jual' => $request->tanggal_jual,
                'tipe_penjualan' => $request->ayam['tipe_penjualan'],
                'diskon' => $request->diskon ?? 0,
                'subtotal' => $request->subtotal,
                'pelanggan_id' => $request->pelanggan_id,
                'user_id' => auth()->id(),
            ]);

            // Simpan detail penjualan dan update stok
            foreach ($details as $detail) {
                $detail['penjualan_id'] = $penjualan->id;
                PenjualanDetail::create($detail);

                // Update stok batch untuk ayam
                if ($detail['batch_id']) {
                    $batch = BatchPembelian::find($detail['batch_id']);
                    if ($batch) {


                        $batch->stok_ekor -= $detail['jumlah_ekor'];
                        if ($detail['jumlah_berat']) {
                            $batch->stok_kg -= $detail['jumlah_berat'];
                        }
                        $batch->save();
                    }
                }
            }

            DB::commit();

            Alert::success('Berhasil', 'Data penjualan berhasil disimpan');
            return redirect()->route('penjualan.index');
        } catch (Exception $e) {
            DB::rollBack();

            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $penjualan = Penjualan::with(['pelanggan', 'penjualanDetails.produk', 'penjualanDetails.batch', 'penjualanDetails.timbangan'])
            ->findOrFail($id);

        return view('penjualan.show', compact('penjualan'));
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $penjualan = Penjualan::findOrFail($id);

            // Kembalikan stok batch
            foreach ($penjualan->penjualanDetails as $detail) {
                if ($detail->batch_id) {
                    $batch = BatchPembelian::find($detail->batch_id);
                    if ($batch) {
                        $batch->stok_ekor += $detail->jumlah_ekor;
                        if ($detail->jumlah_berat) {
                            $batch->stok_kg += $detail->jumlah_berat;
                        }
                        $batch->save();
                    }
                }
            }

            // Hapus penjualan (detail akan terhapus otomatis karena cascade)
            $penjualan->delete();

            DB::commit();

            Alert::success('Berhasil', 'Data penjualan berhasil dihapus');
            return redirect()->route('penjualan.index');
        } catch (\Exception $e) {
            DB::rollBack();

            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function laporanHarian(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'nullable|date',
        ]);

        $tanggal = $validated['tanggal'] ?? now()->toDateString();

        $penjualans = Penjualan::with('pelanggan')
            ->whereDate('tanggal_jual', $tanggal)
            ->withSum([
                'penjualanDetails as jumlah_ekor_produk_1' => function ($query) {
                    $query->where('produk_id', 1);
                },
            ], 'jumlah_ekor')
            ->orderBy('tanggal_jual', 'desc')
            ->get();

        $totalEkor = PenjualanDetail::where('produk_id', 1)
            ->whereHas('penjualan', function ($query) use ($tanggal) {
                $query->whereDate('tanggal_jual', $tanggal);
            })
            ->sum('jumlah_ekor');

        return view('penjualan.laporan-harian', [
            'tanggal' => $tanggal,
            'penjualans' => $penjualans,
            'totalEkor' => $totalEkor,
        ]);
    }

    // API untuk mendapatkan data produk
    public function getProduk($id)
    {
        $produk = Produk::find($id);
        return response()->json($produk);
    }

    // API untuk mendapatkan data batch
    public function getBatch($id)
    {
        $batch = BatchPembelian::find($id);
        return response()->json($batch);
    }
}
