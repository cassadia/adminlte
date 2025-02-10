<!-- Sidebar -->
<div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
            <a href="{{ route('profile.show') }}" class="d-block">{{ Auth::user()->name }}</a>
        </div>
    </div>

    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
            data-accordion="false">
            @foreach ($menusdua as $menu)
                @php
                    $publicPath = session('public_path'); // Ambil nilai dari session
                    $routeName = $menu->menu_link;

                    // Jika pengguna memiliki public path, tambahkan prefix 'public.'
                    if ($publicPath) {
                        $routeName = 'public.' . $menu->menu_link;
                    }
                @endphp
                <li class="nav-item">
                    <a href="{{ route($routeName) }}" class="nav-link">
                        <i class="nav-icon {{ $menu->menu_icon }}"></i>
                        <p>
                            {{ __($menu->menu) }}
                        </p>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    <!-- Sidebar Menu -->
    {{-- <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
            data-accordion="false">
            <li class="nav-item">
                <a href="{{ route('home') }}" class="nav-link">
                    <i class="nav-icon fas fa-th"></i>
                    <p>
                        {{ __('Dashboard') }}
                    </p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('product.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-database"></i>
                    <p>
                        {{ __('Product') }}
                    </p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('vehicle.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-car"></i>
                    <p>
                        {{ __('Vehicle') }}
                    </p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('product.mapping') }}" class="nav-link">
                    <i class="nav-icon fas fa-sitemap"></i>
                    <p>
                        {{ __('Mapping') }}
                    </p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-users"></i>
                    <p>
                        {{ __('Setting Users') }}
                    </p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('cart.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-shopping-cart"></i>
                    <p>
                        {{ __('Cart') }}
                    </p>
                </a>
            </li>
        </ul>
    </nav> --}}
    <!-- /.sidebar-menu -->
</div>
<!-- /.sidebar -->
