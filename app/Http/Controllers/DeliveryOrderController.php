<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Peternak;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;

class DeliveryOrderController extends Controller
{
    public function index()
    {
        $deliveryOrders = DeliveryOrder::all();
        $kodeDO = DeliveryOrder::kodeDO();
        $peternaks = Peternak::all();

        return view('delivery-order.index', compact('deliveryOrders', 'kodeDO', 'peternaks'));
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

            

            // Validasi timbangan
            'nama_karyawan' => 'nullable|string|max:255',

        ], [
            'peternak_id.required' => 'Peternak harus dipilih',
            'peternak_id.exists' => 'Peternak tidak valid',
            'tanggal_do.required' => 'Tanggal DO harus diisi',
            'kode_do.required' => 'Kode DO harus diisi',
            'kode_do.unique' => 'Kode DO sudah digunakan',
            'total_jumlah_ekor.required' => 'Total jumlah ekor harus diisi',
            'total_jumlah_ekor.integer' => 'Total jumlah ekor harus berupa angka',
            'total_berat.required' => 'Total berat harus diisi',
            'total_berat.numeric' => 'Total berat harus berupa angka',
            'nama_karyawan.string' => 'Nama karyawan harus berupa teks',
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
            
            // Redirect ke halaman create pembelian dengan do_id
            return redirect()->route('pembelian.create', ['do_id' => $deliveryOrder->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to create Delivery Order: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $deliveryOrder = DeliveryOrder::findOrFail($id);

        DB::beginTransaction();

        try {
           
        
            // Hapus delivery order
            $deliveryOrder->delete();

            DB::commit();
            Alert::success('Success', 'Delivery Order deleted successfully.');
            return redirect()->route('delivery-order.index')->with('success', 'Delivery Order deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to delete Delivery Order: ' . $e->getMessage()]);
        }
    }
}
