 <aside class="main-sidebar sidebar-dark-warning elevation-4">
     <!-- Brand Logo -->
     <a href="/dashboard" class="brand-link text-center border-bottom-0 pb-3 mt-2">
         <i class="fas fa-crow text-warning mb-1" style="font-size: 2rem;"></i><br>
         <span class="brand-text font-weight-bold h4">SIM<br><small class="text-warning">Kurnia Unggas</small></span>
     </a>

     <!-- Sidebar -->
     <div class="sidebar">
         <!-- Sidebar user panel (optional) -->
         <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
             <div class="image">
                 <i class="fas fa-user-circle text-warning fa-2x"></i>
             </div>
             <div class="info">
                 <a href="#" class="d-block font-weight-bold">{{ auth()->user()->name }}</a>
             </div>
         </div>

         <!-- SidebarSearch Form -->
         <div class="form-inline">
             <div class="input-group" data-widget="sidebar-search">
                 <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                     aria-label="Search">
                 <div class="input-group-append">
                     <button class="btn btn-sidebar">
                         <i class="fas fa-search fa-fw"></i>
                     </button>
                 </div>
             </div>
         </div>

         <!-- Sidebar Menu -->
         <nav class="mt-2">
             <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                 data-accordion="false">
                 <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

                 @foreach ($routes as $route)
                     @if (!$route['is_dropdown'])
                         <li class="nav-item">
                             <a href="{{ route($route['route_name']) }}"
                                 class="nav-link {{ request()->routeIs($route['route_active']) ? 'active' : '' }}">
                                 <i class="nav-icon {{ $route['icon'] }}"></i>
                                 <p>
                                     {{ $route['label'] }}
                                 </p>
                             </a>
                         </li>
                     @else
                         <li class="nav-item {{ request()->routeIs($route['route_active']) ? 'menu-open' : '' }}">
                             <a href="#" class="nav-link">
                                 <i class="nav-icon {{ $route['icon'] }}"></i>
                                 <p>
                                     {{ $route['label'] }}
                                     <i class="right fas fa-angle-left"></i>
                                 </p>
                             </a>
                             <ul class="nav nav-treeview">
                                 @foreach ($route['dropdown'] as $item)
                                     <li class="nav-item">
                                         <a href="{{ route($item['route_name']) }}"
                                             class="nav-link {{ request()->routeIs($item['route_active']) ? 'active' : '' }}">
                                             <i class="{{ $item['icon'] }} nav-icon"></i>
                                             <p>{{ $item['label'] }}</p>
                                         </a>
                                     </li>
                                 @endforeach

                             </ul>
                         </li>
                     @endif
                 @endforeach



             </ul>
         </nav>
         <!-- /.sidebar-menu -->
     </div>
     <!-- /.sidebar -->
 </aside>
