<?php

namespace App\Http\Controllers;

use App\Models\BatchPembelian;
use App\Models\DeliveryOrder;
use App\Models\Keranjang;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Timbangan;
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
        
        return view('pembelian.index', compact('pembelians'));
    }

    public function create()
    {
        // Generate kode pembelian otomatis
        $kodePembelian = Pembelian::generateKodePembelian();
        
        $deliveryOrders = DeliveryOrder::orderBy('kode_do')->get();

        return view('pembelian.create', compact('kodePembelian', 'deliveryOrders'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'peternak_id' => 'required|exists:peternaks,id',
            'tanggal_pembelian' => 'required|date',
            'kode_pembelian' => 'required|string|unique:pembelians,kode_pembelian',
            'status' => 'required|in:belum bayar,sudah bayar',
            
            // Validasi timbangan
            'tanggal_timbangan' => 'required|date',
            'nama_karyawan' => 'nullable|string|max:255',
            
            // Validasi keranjang
            'keranjangs' => 'required|array|min:1',
            'keranjangs.*.jumlah_ekor' => 'required|integer|min:1',
            'keranjangs.*.berat_ayam' => 'required|numeric|min:0',
            
            // Validasi detail pembelian
            'delivery_order_id' => 'nullable|exists:delivery_orders,id',
            'susut_kg' => 'nullable|numeric|min:0',
        ], [
            'peternak_id.required' => 'Peternak harus dipilih',
            'peternak_id.exists' => 'Peternak tidak valid',
            'tanggal_pembelian.required' => 'Tanggal pembelian harus diisi',
            'kode_pembelian.required' => 'Kode pembelian harus diisi',
            'kode_pembelian.unique' => 'Kode pembelian sudah digunakan',
            'status.required' => 'Status harus dipilih',
            'keranjangs.required' => 'Minimal harus ada 1 keranjang',
            'keranjangs.min' => 'Minimal harus ada 1 keranjang',
            'batch_pembelian_id.required' => 'Batch pembelian harus dipilih',
        ]);

        DB::beginTransaction();
        
        try {
            // 1. Hitung total jumlah ekor dan total berat dari keranjang
            $totalJumlahEkor = 0;
            $totalBerat = 0;
            
            foreach ($request->keranjangs as $keranjang) {
                $totalJumlahEkor += $keranjang['jumlah_ekor'];
                $totalBerat += $keranjang['berat_ayam'];
            }

            // 2. Buat data timbangan (jenis otomatis: timbangan data pembelian)
            $timbangan = Timbangan::create([
                'jenis' => 'timbangan data pembelian',
                'tanggal' => $request->tanggal_timbangan,
                'total_jumlah_ekor' => $totalJumlahEkor,
                'total_berat' => $totalBerat,
                'nama_karyawan' => $request->nama_karyawan,
            ]);

            // 3. Simpan data keranjang
            foreach ($request->keranjangs as $keranjangData) {
                Keranjang::create([
                    'timbangan_id' => $timbangan->id,
                    'jumlah_ekor' => $keranjangData['jumlah_ekor'],
                    'berat_ayam' => $keranjangData['berat_ayam'],
                ]);
            }

            // 4. Buat data pembelian
            $pembelian = Pembelian::create([
                'tanggal_pembelian' => $request->tanggal_pembelian,
                'kode_pembelian' => $request->kode_pembelian,
                'status' => $request->status,
                'peternak_id' => $request->peternak_id,
            ]);

            // 5. Buat detail pembelian (harga dan subtotal akan diisi saat pembayaran)
            PembelianDetail::create([
                'pembelian_id' => $pembelian->id,
                'batch_pembelian_id' => $request->batch_pembelian_id,
                'timbangan_id' => $timbangan->id,
                'delivery_order_id' => $request->delivery_order_id,
                'harga_beli_per_kg' => 0, // Akan diisi saat pembayaran
                'subtotal' => 0, // Akan diisi saat pembayaran
                'susut_kg' => $request->susut_kg ?? 0,
            ]);

            // 6. Update stok batch pembelian
            $batch = BatchPembelian::find($request->batch_pembelian_id);
            $beratBersih = $totalBerat - ($request->susut_kg ?? 0);
            
            $batch->stok_ekor += $totalJumlahEkor;
            $batch->stok_kg += $beratBersih;
            $batch->save();

            DB::commit();
            
            Alert::success('Berhasil', 'Data pembelian berhasil disimpan');
            return redirect()->route('pembelian.index');
            
        } catch (\Exception $e) {
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
            'pembelianDetails.deliveryOrder'
        ])->findOrFail($id);
        
        return view('pembelian.show', compact('pembelian'));
    }

    public function edit($id)
    {
        $pembelian = Pembelian::with('pembelianDetails')->findOrFail($id);
        $batchPembelians = BatchPembelian::orderBy('kode_batch')->get();
        $deliveryOrders = DeliveryOrder::orderBy('kode_do')->get();
        
        return view('pembelian.edit', compact('pembelian', 'batchPembelians', 'deliveryOrders'));
    }

    public function update(Request $request, $id)
    {
        // Logic for updating a specific Pembelian
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $pembelian = Pembelian::findOrFail($id);
            
            // Hapus pembelian detail dan data terkait
            foreach ($pembelian->pembelianDetails as $detail) {
                // Update stok batch pembelian
                $batch = $detail->batchPembelian;
                $beratBersih = $detail->timbangan->total_berat - $detail->susut_kg;
                
                $batch->stok_ekor -= $detail->timbangan->total_jumlah_ekor;
                $batch->stok_kg -= $beratBersih;
                $batch->save();
                
                // Hapus keranjang
                $detail->timbangan->keranjangs()->delete();
                
                // Hapus timbangan
                $detail->timbangan->delete();
            }
            
            // Hapus pembelian (akan cascade delete pembelian details)
            $pembelian->delete();
            
            DB::commit();
            
            Alert::success('Berhasil', 'Data pembelian berhasil dihapus');
            return redirect()->route('pembelian.index');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method untuk proses pembayaran pembelian
    public function bayar(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'harga_per_kg' => 'required|integer|min:0',
            'nominal_bayar' => 'required|integer|min:0',
            'tanggal_bayar' => 'required|date',
            'catatan_bayar' => 'nullable|string',
        ], [
            'harga_per_kg.required' => 'Harga per kg harus diisi',
            'harga_per_kg.min' => 'Harga per kg tidak boleh negatif',
            'nominal_bayar.required' => 'Nominal pembayaran harus diisi',
            'nominal_bayar.min' => 'Nominal pembayaran tidak boleh negatif',
            'tanggal_bayar.required' => 'Tanggal pembayaran harus diisi',
        ]);

        DB::beginTransaction();
        
        try {
            // Cari data pembelian
            $pembelian = Pembelian::with('pembelianDetails.timbangan')->findOrFail($id);
            
            // Cek apakah sudah dibayar
            if ($pembelian->status == 'sudah bayar') {
                Alert::warning('Peringatan', 'Pembelian ini sudah dibayar sebelumnya');
                return redirect()->route('pembelian.index');
            }
            
            // Ambil detail pembelian
            $pembelianDetail = $pembelian->pembelianDetails->first();
            
            if (!$pembelianDetail) {
                throw new \Exception('Detail pembelian tidak ditemukan');
            }
            
            // Hitung subtotal
            $totalBerat = $pembelianDetail->timbangan->total_berat;
            $susutKg = $pembelianDetail->susut_kg ?? 0;
            $beratBersih = $totalBerat - $susutKg;
            $subtotal = $beratBersih * $request->harga_per_kg;
            
            // Validasi nominal pembayaran
            if ($request->nominal_bayar < $subtotal) {
                throw new \Exception('Nominal pembayaran kurang dari total yang harus dibayar');
            }
            
            // Update harga beli per kg dan subtotal di pembelian detail
            $pembelianDetail->harga_beli_per_kg = $request->harga_per_kg;
            $pembelianDetail->subtotal = $subtotal;
            $pembelianDetail->save();
            
            // Update status pembelian menjadi sudah bayar
            $pembelian->status = 'sudah bayar';
            $pembelian->save();
            
            DB::commit();
            
            $kembalian = $request->nominal_bayar - $subtotal;
            
            Alert::success('Berhasil', 'Pembayaran berhasil diproses. Kembalian: Rp ' . number_format($kembalian, 0, ',', '.'));
            return redirect()->route('pembelian.index');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Alert::error('Gagal', 'Terjadi kesalahan: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    // Method untuk mengambil data batch pembelian (AJAX)
    public function getBatchPembelian()
    {
        $batchPembelians = BatchPembelian::orderBy('kode_batch')->get();
        return response()->json($batchPembelians);
    }

    // Method untuk mengambil data delivery order (AJAX)
    public function getDeliveryOrder()
    {
        $deliveryOrders = DeliveryOrder::orderBy('kode_do')->get();
        return response()->json($deliveryOrders);
    }
}
