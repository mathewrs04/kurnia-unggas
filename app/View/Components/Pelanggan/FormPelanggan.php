<?php

namespace App\View\Components\Pelanggan;

use App\Models\Pelanggan;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormPelanggan extends Component
{
    /**
     * Create a new component instance.
     */

    public $id, $nama, $alamat, $no_telp;
    public function __construct($id = null)
    {
        if($id){
            $pelanggan = Pelanggan::find($id);
            $this->id = $pelanggan->id;
            $this->nama = $pelanggan->nama;
            $this->alamat = $pelanggan->alamat;
            $this->no_telp = $pelanggan->no_telp;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.pelanggan.form-pelanggan');
    }
}
