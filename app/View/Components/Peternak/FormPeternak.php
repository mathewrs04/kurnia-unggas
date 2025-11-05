<?php

namespace App\View\Components\Peternak;

use App\Models\Pemasok;
use App\Models\Peternak;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormPeternak extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $pemasok_id, $pemasok, $nama, $alamat, $no_telp;
    public function __construct($id = null)
    {
        $this->pemasok = Pemasok::all();
        if($id){
            $peternak = Peternak::find($id);
            $this->id = $peternak->id;
            $this->pemasok_id = $peternak->pemasok_id;
            $this->nama = $peternak->nama;
            $this->alamat = $peternak->alamat;
            $this->no_telp = $peternak->no_telp;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.peternak.form-peternak');
    }
}
