<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            @if($school_identity && $school_identity->logo_path)
                <img src="{{ asset('storage/' . $school_identity->logo_path) }}" alt="logo" height="30" class="me-2">
            @endif
            <div class="col-7 col-xl-9 text-truncate">
                <span class="fw-bolder" title="{{ $school_identity->name }}">
                    {{ $school_identity->name ?? 'SIMS Sekolah' }}
                </span>
            </div>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        @if(auth()->user()->role === 'admin')
            <!-- Masters (Admin Only) -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Data Induk</span></li>
            <li
                class="menu-item {{ request()->is('masters/*') || request()->routeIs('academic.classrooms.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-data"></i>
                    <div>Data Induk</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('masters.teachers.*') ? 'active' : '' }}">
                        <a href="{{ route('masters.teachers.index') }}" class="menu-link">
                            <div>Guru</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('academic.classrooms.*') ? 'active' : '' }}">
                        <a href="{{ route('academic.classrooms.index') }}" class="menu-link">
                            <div>Kelas</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('masters.students.*') ? 'active' : '' }}">
                        <a href="{{ route('masters.students.index') }}" class="menu-link">
                            <div>Siswa</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('masters.subjects.*') ? 'active' : '' }}">
                        <a href="{{ route('masters.subjects.index') }}" class="menu-link">
                            <div>Mata Pelajaran</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('masters.time-slots.*') ? 'active' : '' }}">
                        <a href="{{ route('masters.time-slots.index') }}" class="menu-link">
                            <div>Jam Pelajaran</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        @if(in_array(auth()->user()->role, ['admin', 'teacher']))
            <!-- Akademik -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Akademik</span></li>
            <li class="menu-item {{ request()->routeIs('academic.schedules.*') ? 'active' : '' }}">
                <a href="{{ route('academic.schedules.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-calendar-event"></i>
                    <div>Jadwal Mengajar</div>
                </a>
            </li>

            <!-- Absensi -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Absensi</span></li>
            <li class="menu-item {{ request()->routeIs('transactions.teacher-attendance.*') ? 'active' : '' }}">
                <a href="{{ route('transactions.teacher-attendance.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user-check"></i>
                    <div>Absensi Guru</div>
                </a>
            </li>


            <!-- Jurnal -->
            <li class="menu-item {{ request()->routeIs('transactions.journals.*') ? 'active' : '' }}">
                <a href="{{ auth()->user()->role === 'admin' ? route('transactions.journals.admin-index') : route('transactions.journals.index') }}"
                    class="menu-link">
                    <i class="menu-icon tf-icons bx bx-book-content"></i>
                    <div>{{ auth()->user()->role === 'admin' ? 'Monitoring Jurnal' : 'Jurnal Mengajar' }}</div>
                </a>
            </li>

            <!-- Laporan -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Laporan</span></li>
            <li class="menu-item {{ request()->routeIs('reports.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div>Rekap Laporan</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('reports.teacher-attendance') ? 'active' : '' }}">
                        <a href="{{ route('reports.teacher-attendance') }}" class="menu-link">
                            <div>Absensi Guru</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('reports.student-attendance') ? 'active' : '' }}">
                        <a href="{{ route('reports.student-attendance') }}" class="menu-link">
                            <div>Absensi Siswa</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('reports.teaching-journals') ? 'active' : '' }}">
                        <a href="{{ route('reports.teaching-journals') }}" class="menu-link">
                            <div>Jurnal Mengajar</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        @if(auth()->user()->role === 'admin')
            <!-- Pengaturan (Admin Only) -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Pengaturan</span></li>
            <li class="menu-item {{ request()->routeIs('masters.settings.*') ? 'active' : '' }}">
                <a href="{{ route('masters.settings.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-cog"></i>
                    <div>Konfigurasi</div>
                </a>
            </li>
        @endif
    </ul>
</aside>