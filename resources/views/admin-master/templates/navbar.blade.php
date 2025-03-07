    <!-- Navbar -->
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#menu-toggle" id="menu-toggle">
                <span class="btn btn-default">
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </span>
            </a>

            <span class="navbar-text">
                Halo, <strong>{{ explode(' ', Auth::user()->nama)[0] }}</strong>!

                <a href="{{ route('admin-master.akun.pengaturan') }}"><img
                        class="rounded-circle shadow-4-strong ms-1 me-3" alt="avatar2" height="40px"
                        src="{{ asset('storage/' . Auth::user()->foto) }}"></a>
            </span>
        </div>
    </nav>

    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand">
                <a href="https://www.ukdw.ac.id/akademik/fakultas-bioteknologi/">
                    <img src="{{ asset('main/img/LOGO-FAK-BIOTEK.png') }}" class="logo img-fluid" alt="LOGO-BIOTEK.png">
                </a>
            </li>
            <hr class="my-2 border border-light border-1 opacity-100">
            <li>
                <h5 class="fs-5 text-light fw-bold mt-2 d-flex align-items-center justify-content-between">
                    Utama
                </h5>
                <ul class="list-unstyled ms-4">
                    <li>
                        <a href="{{ route('admin-master.dashboard') }}"
                            class="{{ request()->routeIs('admin-master.dashboard') ? 'active' : '' }}">
                            Dashboard <i class="bi bi-speedometer2 float-end me-4"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-master.rangkuman') }}"
                            class="{{ request()->routeIs('admin-master.rangkuman') ? 'active' : '' }}">
                            Rangkuman <i class="bi bi-graph-up float-end me-4"></i>
                        </a>
                    </li>
                </ul>
            </li>
            <hr class="mt-2 mb-0 border border-light border-1 opacity-100">
            <li>
                <h5 class="fs-5 text-light fw-bold mt-2 d-flex align-items-center justify-content-between"
                    id="tabelDataToggle" style="cursor: pointer;">
                    Tabel Data
                    <i class="bi bi-caret-down-fill me-4" id="tabelCaret"></i>
                </h5>
                <ul class="list-unstyled ms-4 {{ request()->routeIs('admin-master.tabel.*') ? '' : 'collapse' }}"
                    id="tabelDataList">
                    <li>
                        <a href="{{ route('admin-master.tabel.TDS') }}"
                            class="{{ request()->routeIs('admin-master.tabel.TDS') ? 'active' : '' }}">
                            Tabel TDS <i class="bi bi-table float-end me-4"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-master.tabel.udara') }}"
                            class="{{ request()->routeIs('admin-master.tabel.udara') ? 'active' : '' }}">
                            Tabel Udara <i class="bi bi-table float-end me-4"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-master.tabel.arus') }}"
                            class="{{ request()->routeIs('admin-master.tabel.arus') ? 'active' : '' }}">
                            Tabel Arus Air <i class="bi bi-table float-end me-4"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-master.tabel.reservoir') }}"
                            class="{{ request()->routeIs('admin-master.tabel.reservoir') ? 'active' : '' }}">
                            Tabel Reservoir <i class="bi bi-table float-end me-4"></i>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <h5 class="fs-5 text-light fw-bold mt-2 d-flex align-items-center justify-content-between"
                    id="akunToggle" style="cursor: pointer;">
                    Pengaturan
                    <i class="bi bi-caret-down-fill me-4" id="akunCaret"></i>
                </h5>
                <ul class="list-unstyled ms-4  {{ request()->routeIs('admin-master.akun.*') ? '' : 'collapse' }}"
                    id="akunList">
                    <li>
                        <a href="{{ route('admin-master.akun.pengaturan') }}"
                            class="{{ request()->routeIs('admin-master.akun.pengaturan') ? 'active' : '' }}">
                            Akun <i class="bi bi-gear-fill float-end me-4"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin-master.akun.daftar-admin') }}"
                            class="{{ request()->routeIs('admin-master.akun.daftar-admin*') ? 'active' : '' }}">
                            Daftar Admin <i class="bi bi-person-plus-fill float-end me-4"></i>
                        </a>
                    </li>
                </ul>
            </li>
            <hr class="mt-2 mb-0 border border-light border-1 opacity-100">
        </ul>
    </div>
    <!-- /#sidebar-wrapper -->
