<?php

namespace App\View\Components\MetodePembayaran;

use App\Models\MetodePembayaran;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormMetodePembayaran extends Component
{
    public $id;
    public $nama_metode;
    public $keterangan;

    public function __construct($id = null)
    {
        if ($id) {
            $metode = MetodePembayaran::find($id);
            $this->id = $metode->id;
            $this->nama_metode = $metode->nama_metode;
            $this->keterangan = $metode->keterangan;
        }
    }

    public function render(): View|Closure|string
    {
        return view('components.metode-pembayaran.form-metode-pembayaran');
    }
}
