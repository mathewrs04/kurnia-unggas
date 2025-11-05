<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Aside extends Component
{
    /**
     * Create a new component instance.
     */
    public $routes;
    public function __construct()
    {
        $this->routes = [
            [
                "label" => "Dashboard",
                "icon" => "fas fa-tachometer-alt",
                "route_name" => "dashboard",
                "route_active" => "dashboard",
                "is_dropdown" => false
            ],
            [
                "label" => "Master",
                "icon" => "fas fa-database",
                "route_name" => "#",
                "route_active" => "master.*",
                "is_dropdown" => true,
                "dropdown" => [
                    [
                        "label" => "Pemasok",
                        "icon" => "far fa-building",
                        "route_name" => "master.pemasok.index",
                        "route_active" => "master.pemasok.*",
                    ],
                    [
                        "label" => "Peternak",
                        "icon" => "far fa-building",
                        "route_name" => "master.peternak.index",
                        "route_active" => "master.peternak.*",
                    ],
                    [
                        "label" => "Batch Pembelian",
                        "icon" => "far fa-building",
                        "route_name" => "master.batch-pembelian.index",
                        "route_active" => "master.batch-pembelian.*",
                    ],
                    [
                        "label" => "Timbangan",
                        "icon" => "far fa-building",
                        "route_name" => "master.timbangan.index",
                        "route_active" => "master.timbangan.*",
                    ],
                    [
                        "label" => "Delivery Order",
                        "icon" => "far fa-building",
                        "route_name" => "master.delivery-order.index",
                        "route_active" => "master.delivery-order.*",
                    ],
                ]
            ],
            [
                "label" => "Pembelian",
                "icon" => "fas fa-tachometer-alt",
                "route_name" => "pembelian.index",
                "route_active" => "pembelian.*",
                "is_dropdown" => false
            ],
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.aside');
    }
}
