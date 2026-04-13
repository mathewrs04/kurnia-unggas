<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\PembelianDetail;
use App\Models\Timbangan;
use Exception;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;

class DeliveryOrderController extends Controller
{
    public function index()
    {
        $deliveryOrders = DeliveryOrder::get();
        confirmDelete('Hapus Data', 'Yakin hapus data ini?');
        return view('delivery-order.index', compact('deliveryOrders'));
    }

    public function create()
    {
        $kodeDO = DeliveryOrder::kodeDO();
        return view('delivery-order.create', compact('kodeDO'));
    }

    public function store(Request $request)
    {     
        $request->validate([
            'kode_do' => 'required|unique:delivery_orders,kode_do',
            'peternak_id' => 'required|exists:peternaks,id',
            'total_jumlah_ekor' => 'required|integer',
            'total_berat' => 'required|numeric',
            'tanggal_do' => 'required|date',
        ], [
            'peternak_id.required' => 'Peternak harus dipilih',
            'peternak_id.exists' => 'Peternak tidak valid',
            'tanggal_do.required' => 'Tanggal DO harus diisi',
            'kode_do.required' => 'Kode DO harus diisi',
            'kode_do.unique' => 'Kode DO sudah digunakan',
            'total_jumlah_ekor.required' => 'Total jumlah ekor harus diisi',
            'total_jumlah_ekor.integer' => 'Total jumlah ekor harus berupa angka',
            'total_berat.required' => 'Total berat harus diisi',
            'total_berat.numeric' => 'Total berat harus berupa angka'
        ]);
     
        DB::beginTransaction();

        try {             
            $deliveryOrder = DeliveryOrder::updateOrCreate([
                'id' => $request->id ?? null
            ], [
                'kode_do' => $request->kode_do,
                'peternak_id' => $request->peternak_id,
                'total_jumlah_ekor' => $request->total_jumlah_ekor,
                'total_berat' => $request->total_berat,
                'tanggal_do' => $request->tanggal_do
                
            ]);

            DB::commit();
            Alert::success('Berhasil', 'Delivery Order berhasil disimpan. Lanjutkan dengan pembelian.');     
            return redirect()->route('pembelian.create', ['do_id' => $deliveryOrder->id]);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to create Delivery Order: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $deliveryOrder = DeliveryOrder::findOrFail($id);
        return view('delivery-order.show', compact('deliveryOrder'));
    }

    public function edit($id)
    {
        $deliveryOrder = DeliveryOrder::findOrFail($id);
        return view('delivery-order.edit', compact('deliveryOrder'));
    }

    public function update(Request $request, $id)
    {        
        $deliveryOrder = DeliveryOrder::findOrFail($id);
        $request->validate([
            'kode_do' => 'required|unique:delivery_orders,kode_do,' . $id,
            'peternak_id' => 'required|exists:peternaks,id',
            'total_jumlah_ekor' => 'required|integer',
            'total_berat' => 'required|numeric',
            'tanggal_do' => 'required|date',
        ], [
            'peternak_id.required' => 'Peternak harus dipilih',
            'peternak_id.exists' => 'Peternak tidak valid',
            'tanggal_do.required' => 'Tanggal DO harus diisi',
            'kode_do.required' => 'Kode DO harus diisi',
            'kode_do.unique' => 'Kode DO sudah digunakan',
            'total_jumlah_ekor.required' => 'Total jumlah ekor harus diisi',
            'total_jumlah_ekor.integer' => 'Total jumlah ekor harus berupa angka',
            'total_berat.required' => 'Total berat harus diisi',
            'total_berat.numeric' => 'Total berat harus berupa angka'
        ]);
        DB::beginTransaction();
        try {
            $deliveryOrder->update([
                'kode_do' => $request->kode_do,
                'peternak_id' => $request->peternak_id,
                'total_jumlah_ekor' => $request->total_jumlah_ekor,
                'total_berat' => $request->total_berat,
                'tanggal_do' => $request->tanggal_do
            ]);

            $pembelianDetail = PembelianDetail::where('delivery_order_id', $id)->first();
            if ($pembelianDetail && $pembelianDetail->timbangan_id) {
                $timbangan = Timbangan::find($pembelianDetail->timbangan_id);

                if ($timbangan) {
                $pembelianDetail->update([
                    'susut_kg' => $request->total_berat - $timbangan->total_berat,
                ]);
                }
            }

            DB::commit();
            Alert::success('Berhasil', 'Delivery Order berhasil diperbarui.');
            return redirect()->route('delivery-order.index')->with('success', 'Delivery Order updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to update Delivery Order: ' . $e->getMessage()]);
        }

    }

    public function destroy($id)
    {
        $deliveryOrder = DeliveryOrder::findOrFail($id);
        $deliveryOrder->delete();
        toast()->success('Data berhasil dihapus!');
        return redirect()->route('delivery-order.index');
    }
}
