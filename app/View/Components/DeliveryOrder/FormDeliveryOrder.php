<?php

namespace App\View\Components\DeliveryOrder;

use App\Models\DeliveryOrder;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormDeliveryOrder extends Component
{
    public $id, $kode_do, $peternak_id, $total_jumlah_ekor, $total_berat, $tanggal_do;
    public function __construct($id = null)
    {
       if($id){
            $deliveryOrder = DeliveryOrder::find($id);
            $this->id = $deliveryOrder->id;
            $this->peternak_id = $deliveryOrder->peternak_id;
            $this->total_jumlah_ekor = $deliveryOrder->total_jumlah_ekor;
            $this->total_berat = $deliveryOrder->total_berat;
            $this->tanggal_do = Carbon::parse($deliveryOrder->tanggal_do)->format('Y-m-d');
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.delivery-order.form-delivery-order');
    }
}
