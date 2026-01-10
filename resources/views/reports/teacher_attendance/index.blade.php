@extends('layouts.admin')

@section('title', 'Laporan Absensi Guru')
@section('page-title', 'Laporan Absensi Guru')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Laporan Absensi Guru</h5>
            <button onclick="window.print()" class="btn btn-secondary btn-sm no-print">
                <i class="bx bx-printer me-1"></i> Print / PDF
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.teacher-attendance') }}" method="GET" class="row g-3 mb-4 no-print">
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <select name="month" class="form-select">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tahun</label>
                    <select name="year" class="form-select">
                        @foreach(range(date('Y') - 1, date('Y') + 1) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>

            <div class="mb-4 print-only">
                <div class="row align-items-center mb-3">
                    <div class="col-2 text-center">
                        @if($school_identity && $school_identity->logo_path)
                            <img src="{{ asset('storage/' . $school_identity->logo_path) }}" alt="logo" height="80">
                        @endif
                    </div>
                    <div class="col-8 text-center">
                        <h2 class="mb-1">{{ $school_identity->name ?? 'SMP Islam Syiar' }}</h2>
                        <p class="mb-0">{{ $school_identity->address ?? 'Alamat Sekolah' }}</p>
                        <p class="mb-0">Telp: {{ $school_identity->phone ?? '-' }} | Website:
                            {{ $school_identity->website ?? '-' }}
                        </p>
                        <p class="mb-0">Email: {{ $school_identity->email ?? '-' }}</p>
                    </div>
                    <div class="col-2"></div>
                </div>
                <hr style="border: 2px solid #000;">
                <h4 class="text-center mb-4">LAPORAN ABSENSI GURU</h4>
                <div class="row">
                    <div class="col-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="100">Bulan</td>
                                <td width="10">:</td>
                                <td>{{ \Carbon\Carbon::create()->month($month)->isoFormat('MMMM') }}</td>
                            </tr>
                            <tr>
                                <td>Tahun</td>
                                <td>:</td>
                                <td>{{ $year }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Guru</th>
                            <th>NIP</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Izin</th>
                            <th class="text-center">Sakit</th>
                            <th class="text-center">Alpha</th>
                            <th class="text-center">Telat</th>
                            <th class="text-center">Total %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teachers as $teacher)
                            @php
                                $totalDays = $teacher->stats['hadir'] + $teacher->stats['izin'] + $teacher->stats['sakit'] + $teacher->stats['alpha'];
                                $percentage = $totalDays > 0 ? round(($teacher->stats['hadir'] / $totalDays) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td><strong>{{ $teacher->name }}</strong></td>
                                <td>{{ $teacher->nip }}</td>
                                <td class="text-center"><span
                                        class="badge bg-label-success">{{ $teacher->stats['hadir'] }}</span></td>
                                <td class="text-center"><span class="badge bg-label-info">{{ $teacher->stats['izin'] }}</span>
                                </td>
                                <td class="text-center"><span
                                        class="badge bg-label-warning">{{ $teacher->stats['sakit'] }}</span></td>
                                <td class="text-center"><span
                                        class="badge bg-label-danger">{{ $teacher->stats['alpha'] }}</span></td>
                                <td class="text-center"><span
                                        class="badge bg-label-secondary">{{ $teacher->stats['telat'] }}</span></td>
                                <td class="text-center"><strong>{{ $percentage }}%</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-5 print-only">
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4 text-center">
                        <p class="mb-5">
                            Tasikmalaya, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                            Kepala Sekolah
                        </p>
                        <br><br>
                        <p class="fw-bold text-decoration-underline mb-0">
                            {{ $school_identity->headmaster_name ?? 'Kepala Sekolah' }}
                        </p>
                        <small>NIP. {{ $school_identity->headmaster_nip ?? '-' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <style>
        @media print {

            .layout-navbar,
            .layout-menu,
            .no-print,
            .btn,
            .footer {
                display: none !important;
            }

            .content-wrapper {
                padding: 0 !important;
                margin: 0 !important;
            }

            .container-xxl {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .card-header {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            body {
                background-color: white !important;
            }
        }

        .print-only {
            display: none;
        }
    </style>
@endsection