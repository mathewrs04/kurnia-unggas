<?php

namespace App\View\Components\Holiday;

use App\Models\Holiday;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormHoliday extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $name, $date, $pre_days, $post_days;
    public function __construct($id = null)
    {
        if($id){
            $holiday = Holiday::find($id);
            $this->id = $holiday->id;
            $this->name = $holiday->name;
            $this->date = $holiday->date;
            $this->pre_days = $holiday->pre_days;
            $this->post_days = $holiday->post_days;
        }

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.holiday.form-holiday');
    }
}
