<?php

namespace App\Http\Controllers;

use App\Models\BatchPembelian;
use App\Models\DeliveryOrder;
use App\Models\Karyawan;
use App\Models\Keranjang;
use App\Models\MetodePembayaran;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Timbangan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class PembelianController extends Controller
{
    public function index()
    {
        $pembelians = Pembelian::with(['peternak', 'pembelianDetails.timbangan'])
            ->latest()
            ->get();   
        $metodePembayarans = MetodePembayaran::orderBy('nama_metode')->get();

        return view('pembelian.index', compact('pembelians', 'metodePembayarans'));
    }

    public function create(Request $request)
    {
        $kodePembelian = Pembelian::generateKodePembelian();
        $deliveryOrders = DeliveryOrder::with('peternak')
            ->whereDoesntHave('pembelianDetail')
            ->orderBy('kode_do')
            ->get();
        $karyawans = Karyawan::orderBy('nama')->get();
        
        // Ambil data DO jika ada dari parameter
        $selectedDO = null;
        if ($request->has('do_id')) {
            $selectedDO = DeliveryOrder::with('peternak')->find($request->do_id);
        }

        return view('pembelian.create', compact('kodePembelian', 'deliveryOrders', 'selectedDO', 'karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'peternak_id' => 'required|exists:peternaks,id',
            'tanggal_pembelian' => 'required|date',
            'kode_pembelian' => 'required|string|unique:pembelians,kode_pembelian',

            // Validasi timbangan
            'karyawan_ids'    => 'nullable|array',
            'karyawan_ids.*'  => 'integer|exists:karyawans,id',

            // Validasi keranjang
            'keranjangs' => 'required|array|min:1',
            'keranjangs.*.jumlah_ekor' => 'required|integer|min:1',
            'keranjangs.*.berat_keranjang' => 'required|numeric|min:0',
            'keranjangs.*.berat_total' => 'required|numeric|min:0',
            'keranjangs.*.berat_ayam' => 'required|numeric|min:0',

            // Validasi detail pembelian 
            'delivery_order_id' => 'required|exists:delivery_orders,id',
            'susut_kg' => 'nullable|numeric',
        ], [
            'peternak_id.required' => 'Peternak harus dipilih',
            'peternak_id.exists' => 'Peternak tidak valid',
            'tanggal_pembelian.required' => 'Tanggal pembelian harus diisi',
            'kode_pembelian.required' => 'Kode pembelian harus diisi',
            'kode_pembelian.unique' => 'Kode pembelian sudah digunakan',
            'keranjangs.required' => 'Minimal harus ada 1 keranjang',
            'keranjangs.min' => 'Minimal harus ada 1 keranjang',
            'delivery_order_id.required' => 'Delivery Order harus dipilih',
            'delivery_order_id.exists' => 'Delivery Order tidak valid',
        ]);

        DB::beginTransaction();

        try {
            //Hitung total jumlah ekor dan total berat dari keranjang
            $totalJumlahEkor = 0;
            $totalBerat = 0;

            foreach ($request->keranjangs as $keranjang) {
                $totalJumlahEkor += $keranjang['jumlah_ekor'];
                $totalBerat += $keranjang['berat_ayam'];
            }

            //Buat data timbangan (jenis otomatis: timbangan_data_pembelian)
            $timbangan = Timbangan::create([
                'jenis'             => 'timbangan_data_pembelian',
                'tanggal'           => $request->tanggal_pembelian,
                'total_jumlah_ekor' => $totalJumlahEkor,
                'total_berat'       => $totalBerat,
            ]);
            $timbangan->karyawans()->sync($request->karyawan_ids ?? []);

            //Simpan data keranjang
            foreach ($request->keranjangs as $keranjangData) {
                Keranjang::create([
                    'timbangan_id' => $timbangan->id,
                    'jumlah_ekor' => $keranjangData['jumlah_ekor'],
                    'berat_keranjang' => $keranjangData['berat_keranjang'],
                    'berat_total' => $keranjangData['berat_total'],
                    'berat_ayam' => $keranjangData['berat_ayam'],
                ]);
            }

            //Buat data pembelian dengan status otomatis belum bayar
            $pembelian = Pembelian::create([
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'kode_pembelian' => $request->kode_pembelian,
                'status' => Pembelian::STATUS_BELUM_BAYAR,
                'peternak_id' => $request->peternak_id,
                'user_id' => auth()->id(),
            ]);

            //Generate kode batch otomatis
            $prefix = 'BATCH-';
            $maxId = BatchPembelian::max('id');
            $kodeBatch = $prefix . str_pad($maxId + 1, 5, '0', STR_PAD_LEFT);

            //Buat data batch pembelian
            $batchPembelian = BatchPembelian::create([
                'kode_batch' => $kodeBatch,
                'stok_ekor' => $totalJumlahEkor,
                'stok_kg' => $totalBerat,
            ]);

            //Buat detail pembelian (harga dan subtotal akan diisi saat pembayaran)
            PembelianDetail::create([
                'pembelian_id' => $pembelian->id,
                'batch_pembelian_id' => $batchPembelian->id,
                'produk_id' => 1,
                'timbangan_id' => $timbangan->id,
                'delivery_order_id' => $request->delivery_order_id,
                'susut_kg' => $request->susut_kg,
            ]);


            DB::commit();

            Alert::success('Berhasil', 'Data pembelian berhasil disimpan');
            return redirect()->route('pembelian.index');
        } catch (Exception $e) {
            DB::rollBack();

            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show($id)
    {
        $pembelian = Pembelian::with([
            'peternak',
            'pembelianDetails.batchPembelian',
            'pembelianDetails.timbangan.keranjangs',
            'pembelianDetails.timbangan.karyawans',
            'pembelianDetails.deliveryOrder'
        ])->findOrFail($id);

        return view('pembelian.show', compact('pembelian'));
    }

    public function edit($id)
    {
        $pembelian = Pembelian::with([
            'peternak',
            'pembelianDetails.timbangan.keranjangs',
            'pembelianDetails.timbangan.karyawans',
            'pembelianDetails.deliveryOrder'
        ])->findOrFail($id);
        
        $detail = $pembelian->pembelianDetails->first();
        $timbangan = $detail ? $detail->timbangan : null;
        $keranjangs = $timbangan ? $timbangan->keranjangs : collect();
        $currentDeliveryOrderId = $detail ? $detail->delivery_order_id : null;
        $selectedKaryawanIds = old('karyawan_ids', $timbangan ? $timbangan->karyawans->pluck('id')->toArray() : []);
        $keranjangCount = $keranjangs->count() > 0 ? $keranjangs->count() : 1;

        $deliveryOrders = DeliveryOrder::with('peternak')
            ->whereDoesntHave('pembelianDetail')
            ->when($currentDeliveryOrderId, function ($query) use ($currentDeliveryOrderId) {
                $query->orWhere('id', $currentDeliveryOrderId);
            })
            ->orderBy('kode_do')
            ->get();

        $karyawans = Karyawan::orderBy('nama')->get();

        return view('pembelian.edit', compact(
            'pembelian',
            'deliveryOrders',
            'karyawans',
            'detail',
            'timbangan',
            'keranjangs',
            'currentDeliveryOrderId',
            'selectedKaryawanIds',
            'keranjangCount'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'peternak_id' => 'required|exists:peternaks,id',
            'tanggal_pembelian' => 'required|date',
            'karyawan_ids'    => 'nullable|array',
            'karyawan_ids.*'  => 'integer|exists:karyawans,id',
            'keranjangs'      => 'required|array|min:1',
            'keranjangs.*.jumlah_ekor' => 'required|integer|min:1',
            'keranjangs.*.berat_keranjang' => 'required|numeric|min:0',
            'keranjangs.*.berat_total' => 'required|numeric|min:0',
            'keranjangs.*.berat_ayam'  => 'required|numeric|min:0',
            'delivery_order_id'        => 'nullable|exists:delivery_orders,id',
        ], [
            'peternak_id.required' => 'Peternak harus dipilih',
            'tanggal_pembelian.required' => 'Tanggal pembelian harus diisi',
            'keranjangs.required'         => 'Minimal harus ada 1 keranjang',
        ]);

        DB::beginTransaction();

        try {
            $pembelian = Pembelian::with('pembelianDetails.timbangan.keranjangs')->findOrFail($id);

            // Cek apakah sudah dibayar
            if ($pembelian->status == Pembelian::STATUS_SUDAH_BAYAR) {
                Alert::warning('Peringatan', 'Pembelian yang sudah dibayar tidak dapat diubah');
                return redirect()->route('pembelian.show', $id);
            }

            // Update data pembelian
            $pembelian->update([
                'peternak_id' => $request->peternak_id,
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'status' => $request->delivery_order_id ? Pembelian::STATUS_BELUM_BAYAR : $pembelian->status
            ]);

            // Hitung total dari keranjang
            $totalJumlahEkor = 0;
            $totalBerat = 0;

            foreach ($request->keranjangs as $keranjang) {
                $totalJumlahEkor += $keranjang['jumlah_ekor'];
                $totalBerat += $keranjang['berat_ayam'];
            }

            // Update timbangan
            $detail = $pembelian->pembelianDetails->first();
            if ($detail && $detail->timbangan) {
                $timbangan = $detail->timbangan;
                $timbangan->update([
                    'tanggal'           => $request->tanggal_pembelian,
                    'total_jumlah_ekor' => $totalJumlahEkor,
                    'total_berat'       => $totalBerat,
                ]);
                $timbangan->karyawans()->sync($request->karyawan_ids ?? []);

                // Hapus keranjang lama
                $timbangan->keranjangs()->delete();

                // Tambah keranjang baru
                foreach ($request->keranjangs as $keranjangData) {
                    Keranjang::create([
                        'timbangan_id' => $timbangan->id,
                        'jumlah_ekor' => $keranjangData['jumlah_ekor'],
                        'berat_keranjang' => $keranjangData['berat_keranjang'],
                        'berat_total' => $keranjangData['berat_total'],
                        'berat_ayam' => $keranjangData['berat_ayam'],
                    ]);
                }

                // Update delivery order di detail pembelian
                $susutKg = 0;
                if ($request->delivery_order_id) {
                    $deliveryOrder = DeliveryOrder::findOrFail($request->delivery_order_id);
                    $susutKg = $deliveryOrder->total_berat - $totalBerat;
                }

                $detail->update([
                    'delivery_order_id' => $request->delivery_order_id,
                    'susut_kg' => $susutKg,
                ]);

                // Update batch pembelian stok
                $batch = $detail->batchPembelian;
                if ($batch) {
                    $batch->update([
                        'stok_ekor' => $totalJumlahEkor,
                        'stok_kg' => $totalBerat,
                    ]);
                }
            }

            DB::commit();

            Alert::success('Berhasil', 'Data pembelian berhasil diupdate');
            return redirect()->route('pembelian.show', $id);
        } catch (Exception $e) {
            DB::rollBack();

            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $pembelian = Pembelian::findOrFail($id);

            // Cek apakah sudah dibayar
            if ($pembelian->status == Pembelian::STATUS_SUDAH_BAYAR) {
                Alert::warning('Peringatan', 'Pembelian yang sudah dibayar tidak dapat dihapus');
                return redirect()->route('pembelian.index');
            }

            // Soft delete pembelian detail dan data terkait
            foreach ($pembelian->pembelianDetails as $detail) {
                // Soft delete keranjang
                if ($detail->timbangan) {
                    $detail->timbangan->keranjangs()->delete();

                    // Soft delete timbangan
                    $detail->timbangan->delete();
                }

                // Soft delete pembelian detail
                $detail->delete();
            }

            // Soft delete pembelian
            $pembelian->delete();

            DB::commit();

            Alert::success('Berhasil', 'Data pembelian berhasil dihapus');
            return redirect()->route('pembelian.index');
        } catch (Exception $e) {
            DB::rollBack();

            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method untuk proses pembayaran pembelian
    public function bayar(Request $request, $id)
    {
        $request->validate([
            'harga_per_kg' => 'required|integer|min:0',
            'subtotal' => 'required|integer|min:0',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|exists:metode_pembayarans,id',
        ], [
            'harga_per_kg.required' => 'Harga per kg harus diisi',
            'harga_per_kg.min' => 'Harga per kg tidak boleh negatif',
            'tanggal_bayar.required' => 'Tanggal pembayaran harus diisi',
            'subtotal.required' => 'Subtotal harus diisi',
            'subtotal.min' => 'Subtotal tidak boleh negatif',
            'metode_pembayaran.required' => 'Metode pembayaran harus dipilih',
            'metode_pembayaran.exists' => 'Metode pembayaran tidak valid',
        ]);

        DB::beginTransaction();

        try {
            // Cari data pembelian
            $pembelian = Pembelian::with('pembelianDetails.deliveryOrder')->findOrFail($id);

            // Cek status pembelian
            if ($pembelian->status == Pembelian::STATUS_SUDAH_BAYAR) {
                Alert::warning('Peringatan', 'Pembelian ini sudah dibayar sebelumnya');
                return redirect()->route('pembelian.index');
            }

            // Ambil detail pembelian
            $pembelianDetail = $pembelian->pembelianDetails->first();

            if (!$pembelianDetail) {
                throw new Exception('Detail pembelian tidak ditemukan');
            }

            // Hitung subtotal
            $totalBerat = $pembelianDetail->timbangan->total_berat;
            $subtotal = $totalBerat * $request->harga_per_kg;

            // Update harga beli per kg dan subtotal di pembelian detail
            $pembelianDetail->update([
                'harga_beli_per_kg' => $request->harga_per_kg,
                'metode_pembayaran_id' => $request->metode_pembayaran,
                'subtotal' => $subtotal,
                'tanggal_bayar' => $request->tanggal_bayar,
            ]);

            // Update harga beli per kg di batch pembelian
            if ($pembelianDetail->batchPembelian) {
                $pembelianDetail->batchPembelian->update([
                    'harga_beli_per_kg' => $request->harga_per_kg,
                ]);
            }

            // Update status pembelian menjadi sudah bayar
            $pembelian->update([
                'status' => Pembelian::STATUS_SUDAH_BAYAR,
            ]);

            DB::commit();


            Alert::success('Berhasil', 'Pembayaran berhasil diproses.');
            return redirect()->route('pembelian.index');
        } catch (Exception $e) {
            DB::rollBack();

            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back();
        }
    }

}
