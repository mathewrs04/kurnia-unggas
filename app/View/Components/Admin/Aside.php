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
        $user = auth()->user();
        $allRoutes = [
            [
                "label" => "Dashboard",
                "icon" => "fas fa-tachometer-alt",
                "route_name" => "dashboard",
                "route_active" => "dashboard",
                "is_dropdown" => false,
                "roles" => ['pemilik', 'penanggung_jawab', 'kasir']
            ],
            [
                "label" => "Master",
                "icon" => "fas fa-database",
                "route_name" => "#",
                "route_active" => "master.*",
                "is_dropdown" => true,
                "roles" => ['penanggung_jawab'],
                "dropdown" => [
                    [
                        "label" => "Pemasok",
                        "icon" => "fas fa-truck-loading",
                        "route_name" => "master.pemasok.index",
                        "route_active" => "master.pemasok.*",
                    ],
                    [
                        "label" => "Peternak",
                        "icon" => "fas fa-user-friends",
                        "route_name" => "master.peternak.index",
                        "route_active" => "master.peternak.*",
                    ],
                    [
                        "label" => "Batch Pembelian",
                        "icon" => "fas fa-tags",
                        "route_name" => "master.batch-pembelian.index",
                        "route_active" => "master.batch-pembelian.*",
                    ],
                    [
                        "label" => "Pelanggan",
                        "icon" => "fas fa-users",
                        "route_name" => "master.pelanggan.index",
                        "route_active" => "master.pelanggan.*",
                    ],
                    [
                        "label" => "Produk",
                        "icon" => "fas fa-box",
                        "route_name" => "master.produk.index",
                        "route_active" => "master.produk.*",
                    ],
                    [
                        "label" => "Metode Pembayaran",
                        "icon" => "fas fa-credit-card",
                        "route_name" => "master.metode-pembayaran.index",
                        "route_active" => "master.metode-pembayaran.*",
                    ],
                    [
                        "label" => "Holiday",
                        "icon" => "fas fa-calendar-alt",
                        "route_name" => "master.holiday.index",
                        "route_active" => "master.holiday.*",
                    ],
                    [
                        "label" => "Harga Ayam",
                        "icon" => "fas fa-money-bill-wave",
                        "route_name" => "master.harga-ayam.index",
                        "route_active" => "master.harga-ayam.*",
                    ],
                    [
                        "label" => "Karyawan",
                        "icon" => "fas fa-id-badge",
                        "route_name" => "master.karyawan.index",
                        "route_active" => "master.karyawan.*",
                    ]
                ]
            ],
            [
                "label" => "Delivery Order",
                "icon" => "far fa-building",
                "route_name" => "delivery-order.index",
                "route_active" => "delivery-order.*",
                "is_dropdown" => false,
                "roles" => ['penanggung_jawab']
            ],
            [
                "label" => "Pembelian",
                "icon" => "fas fa-tachometer-alt",
                "route_name" => "pembelian.index",
                "route_active" => "pembelian.*",
                "is_dropdown" => false,
                "roles" => ['penanggung_jawab']
            ],
            [
                "label" => "Penjualan",
                "icon" => "fas fa-shopping-cart",
                "route_name" => "penjualan.index",
                "route_active" => "penjualan.index",
                "is_dropdown" => false,
                "roles" => ['penanggung_jawab', 'kasir']
            ],
            [
                "label" => "Stok Opname",
                "icon" => "fas fa-clipboard-check",
                "route_name" => "stok-opname.index",
                "route_active" => "stok-opname.*",
                "is_dropdown" => false,
                "roles" => ['penanggung_jawab']
            ],
            [
                "label" => "Mortalitas Ayam",
                "icon" => "fas fa-skull-crossbones",
                "route_name" => "mortalitas-ayam.index",
                "route_active" => "mortalitas-ayam.*",
                "is_dropdown" => false,
                "roles" => ['penanggung_jawab']
            ],
            [
                "label" => "Susut Batch",
                "icon" => "fas fa-balance-scale",
                "route_name" => "susut-batch.index",
                "route_active" => "susut-batch.index",
                "is_dropdown" => false,
                "roles" => ['penanggung_jawab']
            ],
            [
                "label" => "Laporan Penjualan",
                "icon" => "fas fa-chart-line",
                "route_name" => "penjualan.laporan-harian",
                "route_active" => "penjualan.laporan-harian",
                "is_dropdown" => false,
                "roles" => ['pemilik', 'penanggung_jawab']
            ],
            [
                "label" => "Biaya Operasional",
                "icon" => "fas fa-file-invoice-dollar",
                "route_name" => "biaya-operasional.index",
                "route_active" => "biaya-operasional.*",
                "is_dropdown" => false,
                "roles" => ['penanggung_jawab']
            ],
            [
                "label" => "Forecast",
                "icon" => "fas fa-chart-bar",
                "route_name" => "forecast.index",
                "route_active" => "forecast.index",
                "is_dropdown" => false,
                "roles" => ['pemilik', 'penanggung_jawab']
            ],
            [
                "label" => "Rekomendasi Pembelian",
                "icon" => "fas fa-chart-line",
                "route_name" => "forecast.rekomendasi",
                "route_active" => "forecast.rekomendasi",
                "is_dropdown" => false,
                "roles" => ['penanggung_jawab']
            ],
            [
                "label" => "Report",
                "icon" => "fas fa-database",
                "route_name" => "#",
                "route_active" => "report.*",
                "is_dropdown" => true,
                "roles" => ['penanggung_jawab'],
                "dropdown" => [
                    [
                        "label" => "Timbangan",
                        "icon" => "far fa-building",
                        "route_name" => "report.timbangan.index",
                        "route_active" => "report.timbangan.*",
                    ],
                    [
                        "label" => "Laporan Keuntungan",
                        "icon" => "far fa-building",
                        "route_name" => "report.keuntungan.index",
                        "route_active" => "report.keuntungan.*",
                    ]
                ]
            ],

        ];

        // Filter routes based on user role
        $this->routes = collect($allRoutes)->filter(function ($route) use ($user) {
            return in_array($user->role, $route['roles']);
        })->values()->toArray();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.aside');
    }
}
