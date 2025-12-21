<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('map.index') }}" class="nav-link {{ request()->routeIs('map.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-map-marked-alt"></i>
                    <p>Peta Interaktif</p>
                </a>
            </li>
            @if (Auth::user()->isAdmin())
            <li class="nav-header">MANAJEMEN DATA</li>
            <li class="nav-item {{ request()->routeIs('admin.disaster-zones.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.disaster-zones.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-exclamation-triangle"></i>
                    <p>
                        Zona Bencana
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.disaster-zones.index') }}" class="nav-link {{ request()->routeIs('admin.disaster-zones.index') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Daftar Zona</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.disaster-zones.create') }}" class="nav-link {{ request()->routeIs('admin.disaster-zones.create') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Tambah Zona Baru</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.evacuation-routes.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.evacuation-routes.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-route"></i>
                    <p>
                        Rute Evakuasi
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.evacuation-routes.index') }}" class="nav-link {{ request()->routeIs('admin.evacuation-routes.index') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Daftar Rute</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.evacuation-routes.create') }}" class="nav-link {{ request()->routeIs('admin.evacuation-routes.create') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Tambah Rute Baru</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.evacuation-facilities.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.evacuation-facilities.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-building"></i>
                    <p>
                        Fasilitas Evakuasi
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.evacuation-facilities.index') }}" class="nav-link {{ request()->routeIs('admin.evacuation-facilities.index') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Daftar Fasilitas</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.evacuation-facilities.create') }}" class="nav-link {{ request()->routeIs('admin.evacuation-facilities.create') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Tambah Fasilitas Baru</p>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item {{ request()->routeIs('admin.aid-distribution-points.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('admin.aid-distribution-points.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-hand-holding-heart"></i>
                    <p>
                        Titik Distribusi Bantuan
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('admin.aid-distribution-points.index') }}" class="nav-link {{ request()->routeIs('admin.aid-distribution-points.index') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Daftar Titik</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.aid-distribution-points.create') }}" class="nav-link {{ request()->routeIs('admin.aid-distribution-points.create') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Tambah Titik Baru</p>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->