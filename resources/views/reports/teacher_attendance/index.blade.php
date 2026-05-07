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

            <div class="mb-1 print-only">
                <div class="row align-items-center mb-2">
                    <div class="col-2 text-center">
                        @if($school_identity && $school_identity->logo_path)
                            <img src="{{ asset('storage/' . $school_identity->logo_path) }}" alt="logo" height="60">
                        @endif
                    </div>
                    <div class="col-8 text-center">
                        <p class="mb-1 fw-bold">YAYASAN BISYRIL ARIFIN</p>
                        <p class="mb-1 fw-bold">SMP ISLAM “ BISYRIL ARIFIN “</p>
                        <p class="mb-1 fw-bold">SOGAAN PAKUNIRAN PROBOLINGGO</p>
                        <p class="mb-1">NSS : 202052024003/20570916 Terakreditasi : B</p>
                        <p class="mb-1">Sekertariat : Jl PP Bisyril Arifin - Sogaan – Pakuniran –Utara Lapangan Kode Post 67292.082359386122</p>
                    </div>
                    <div class="col-2"></div>
                </div>
                <hr style="border: 2px solid #000; margin: 0.35rem 0 0.5rem;">
                <h4 class="text-center mb-0">LAPORAN ABSENSI GURU</h4>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="fw-bold" width="100">Bulan / Tahun</td>
                                <td width="10">:</td>
                                <td>{{ \Carbon\Carbon::create()->month($month)->isoFormat('MMMM') }} {{ $year }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="table-responsive text-nowrap mt-0">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Guru</th>
                            <th>NIY</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Izin</th>
                            <th class="text-center">Sakit</th>
                            <th class="text-center">Alpha</th>
                            <th class="text-center">Telat</th>
                            <th class="text-center">Mengajar</th>
                            <th class="text-center">Piket</th>
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
                                <td class="text-center"><span
                                        class="badge bg-label-primary">{{ $teacher->stats['mengajar'] }}</span></td>
                                <td class="text-center"><span
                                        class="badge bg-label-info text-dark">{{ $teacher->stats['piket'] }}</span></td>
                                <td class="text-center"><strong>{{ $percentage }}%</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-1 print-only signature-block">
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4 text-center">
                        <p class="mb-5">
                            Probolinggo, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}<br>
                            Kepala Sekolah
                        </p>
                        <div class="mb-2"></div>
                        <p class="fw-bold text-decoration-underline mb-0">
                            {{ $school_identity->headmaster_name ?? 'Kepala Sekolah' }}
                        </p>
                        <small>NIY. {{ $school_identity->headmaster_nip ?? '-' }}</small>
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

        @media print {
            @page {
                size: landscape;
                margin: 10mm;
            }

            .table {
                font-size: 0.78rem;
                border-collapse: collapse;
            }

            .table th,
            .table td {
                padding: 0.28rem 0.35rem !important;
                vertical-align: middle !important;
            }

            .print-only .fw-bold,
            .print-only p {
                margin-bottom: 0.12rem;
                line-height: 1.05;
            }

            .print-only h4 {
                margin-bottom: 0.4rem;
                line-height: 1.1;
                font-size: 1rem;
            }

            .print-only .row {
                margin-bottom: 0.15rem;
            }

            .table-responsive,
            .table-responsive .table {
                page-break-inside: auto;
                break-inside: auto;
                -webkit-print-color-adjust: exact;
            }

            .table tbody,
            .table tr,
            .table th,
            .table td {
                page-break-inside: auto;
                break-inside: auto;
            }

            .signature-block {
                margin-top: 0.7rem;
                page-break-inside: avoid;
                page-break-before: avoid;
            }

            .print-only {
                display: block !important;
            }
        }
    </style>
@endsection