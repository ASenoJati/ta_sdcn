<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="./index.html" class="brand-link">
            <!--begin::Brand Image-->
            <img
                src="{{ asset('assets/img/AdminLTELogo.png') }}"
                alt="AdminLTE Logo"
                class="brand-image opacity-75 shadow" />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">SDCN</span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
    </div>
    <!--end::Sidebar Brand-->
    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
                class="nav sidebar-menu flex-column"
                data-lte-toggle="treeview"
                role="navigation"
                aria-label="Main navigation"
                data-accordion="false"
                id="navigation">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">SISWA</li>
                <li class="nav-item">
                    <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people"></i>
                        <p>Data Siswa</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('classrooms.index') }}" class="nav-link {{ request()->routeIs('classrooms.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-building"></i>
                        <p>Data Kelas</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('subjects.index') }}" class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-book"></i>
                        <p>Data Mata Pelajaran</p>
                    </a>
                </li>

                <li class="nav-header">PEMBELAJARAN</li>
                <li class="nav-item">
                    <a href="{{ route('lesson-hours.index') }}" class="nav-link {{ request()->routeIs('lesson-hours.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-clock-fill"></i>
                        <p>Jam Pembelajaran</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('teaching-schedules.index') }}" class="nav-link {{ request()->routeIs('teaching-schedules.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calendar-check"></i>
                        <p>Jadwal Mengajar</p>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="{{ route('teaching-journals.index') }}" class="nav-link {{ request()->routeIs('teaching-journals.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calendar-check"></i>
                        <p>Jurnal Mengajar</p>
                    </a>
                </li>


                <li class="nav-header">PRESENSI GURU</li>
                <li class="nav-item">
                    <a href="{{ route('location.index') }}" class="nav-link {{ request()->routeIs('location.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-geo-alt"></i>
                        <p>Lokasi</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('role-attendance-times.index') }}" class="nav-link {{ request()->routeIs('role-attendance-times.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-clock"></i>
                        <p>Setting Waktu Presensi</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('attendance-setting.index') }}" class="nav-link {{ request()->routeIs('attendance-setting.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-clock-history"></i>
                        <p>Waktu Presensi</p>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="{{ route('user-attendances.index') }}" class="nav-link {{ request()->routeIs('user-attendances.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-clipboard-check"></i>
                        <p>Data Presensi Guru</p>
                    </a>
                </li>

                <li class="nav-header">USER</li>
                <li class="nav-item">
                    <a href="{{ route('user.index') }}" class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person"></i>
                        <p>Data User</p>
                    </a>
                </li>
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>