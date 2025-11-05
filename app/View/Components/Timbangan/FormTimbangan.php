<?php

namespace App\View\Components\Timbangan;

use App\Models\Timbangan;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormTimbangan extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $jenis, $nama_karyawan;
    public function __construct($id = null)
    {
        if($id){
            $timbangan = Timbangan::find($id);
            $this->id = $timbangan->id;
            $this->jenis = $timbangan->jenis;
            $this->nama_karyawan = $timbangan->nama_karyawan;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.timbangan.form-timbangan');
    }
}
