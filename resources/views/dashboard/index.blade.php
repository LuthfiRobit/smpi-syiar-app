@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="row">
        <!-- Welcome Card -->
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Halo {{ auth()->user()->name }}! 🎉</h5>
                            <p class="mb-4">
                                Selamat datang di Sistem Informasi Manajemen Sekolah (SIMS).
                                Berikut ringkasan aktivitas hari ini.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('sekolah/assets/img/illustrations/man-with-laptop-light.png') }}"
                                height="140" alt="View Badge User" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <!-- Admin Statistics -->
        @if(auth()->user()->role === 'admin')
            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-primary p-2"><i class="bx bx-user-check bx-sm"></i></span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Guru Aktif</span>
                        <h3 class="card-title mb-2">{{ $data['total_teachers'] ?? 0 }}</h3>
                        <small class="text-primary fw-semibold">Total Guru</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-info p-2"><i class="bx bx-group bx-sm"></i></span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Siswa Aktif</span>
                        <h3 class="card-title mb-2">{{ $data['total_students'] ?? 0 }}</h3>
                        <small class="text-info fw-semibold">Total Siswa</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-warning p-2"><i class="bx bx-home bx-sm"></i></span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Kelas</span>
                        <h3 class="card-title mb-2">{{ $data['total_classrooms'] ?? 0 }}</h3>
                        <small class="text-warning fw-semibold">Total Kelas</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-success p-2"><i class="bx bx-pie-chart-alt bx-sm"></i></span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Kehadiran Guru</span>
                        <h3 class="card-title mb-2">{{ $data['attendance_percentage'] ?? 0 }}%</h3>
                        <small class="text-success fw-semibold">Hari Ini</small>
                    </div>
                </div>
            </div>
        @endif

        <!-- Teacher Statistics -->
        @if(auth()->user()->role === 'teacher')
            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-primary p-2"><i class="bx bx-time bx-sm"></i></span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Status Kehadiran</span>
                        @if(isset($data['today_attendance']) && $data['today_attendance'])
                            <h3 class="card-title mb-2 text-success">Hadir</h3>
                            <small class="text-muted">Masuk:
                                {{ \Carbon\Carbon::parse($data['today_attendance']->check_in)->format('H:i') }}</small>
                        @else
                            <h3 class="card-title mb-2 text-danger">Belum Hadir</h3>
                            <small class="text-muted">Silakan Check-in</small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-info p-2"><i class="bx bx-calendar-event bx-sm"></i></span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Jadwal Mengajar</span>
                        <h3 class="card-title mb-2">{{ $data['today_schedules_count'] ?? 0 }}</h3>
                        <small class="text-info fw-semibold">Kelas Hari Ini {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l') }}</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                                <span class="badge bg-label-success p-2"><i class="bx bx-book-content bx-sm"></i></span>
                            </div>
                        </div>
                        <span class="fw-semibold d-block mb-1">Jurnal Terisi</span>
                        <h3 class="card-title mb-2">{{ $data['journals_filled'] ?? 0 }}</h3>
                        <small class="text-success fw-semibold">Hari Ini</small>
                    </div>
                </div>
            </div>
        @endif

        <!-- Info Card -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Selamat Datang di {{ $school_identity->name ?? 'SIMS SMP Islam Syiar' }}</h5>
                    <p class="mb-0">
                        Sistem ini membantu Anda mengelola data sekolah, jadwal mengajar, absensi, dan jurnal pembelajaran.
                        Gunakan menu di sebelah kiri untuk mengakses fitur-fitur yang tersedia.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection