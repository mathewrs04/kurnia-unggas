<?php

namespace App\View\Components\DeliveryOrder;

use App\Models\DeliveryOrder;
use App\Models\Timbangan;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormDeliveryOrder extends Component
{
    public $id = null;
    public $timbangan_id = null;
    public $timbangans;   // pakai plural biar jelas di view
    public $keranjang = []; // opsional, jika ada detail DO

    public function __construct($id = null)
    {
        $this->id = $id; // SELALU set, walau null

        // hanya timbangan jenis DO
        $this->timbangans = Timbangan::where('jenis', 'timbangan data DO')->get();

        if ($id) {
            $deliveryOrder = DeliveryOrder::with('keranjang')->find($id);
            if ($deliveryOrder) {
                $this->timbangan_id = $deliveryOrder->timbangan_id;
                // kalau ada relasi detail keranjang:
                if ($deliveryOrder->relationLoaded('keranjang')) {
                    $this->keranjang = $deliveryOrder->keranjang
                        ->map(fn($d) => [
                            'jumlah_ekor' => $d->jumlah_ekor,
                            'berat'       => $d->berat,
                        ])->toArray();
                }
            }
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.delivery-order.form-delivery-order');
    }
}
