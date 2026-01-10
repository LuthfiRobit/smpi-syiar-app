@extends('layouts.admin')

@section('title', 'Laporan Absensi Siswa')
@section('page-title', 'Laporan Absensi Siswa')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Laporan Absensi Siswa</h5>
            <button onclick="window.print()" class="btn btn-secondary btn-sm no-print">
                <i class="bx bx-printer me-1"></i> Print / PDF
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.student-attendance') }}" method="GET"
                class="row g-3 mb-4 no-print border p-3 rounded">
                <div class="col-md-2">
                    <label class="form-label">Tahun Ajaran</label>
                    <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ $academicYearId == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kelas</label>
                    <select name="classroom_id" class="form-select" required>
                        <option value="">Pilih Kelas</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" {{ $classroomId == $classroom->id ? 'selected' : '' }}>
                                {{ $classroom->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mata Pelajaran (Opsional)</label>
                    <select name="subject_id" class="form-select">
                        <option value="">Semua Mata Pelajaran</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-12 mt-3 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Filter Data</button>
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
                <h4 class="text-center mb-4">LAPORAN ABSENSI SISWA</h4>
                <div class="row">
                    <div class="col-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="130">Kelas</td>
                                <td width="10">:</td>
                                <td>{{ $classrooms->firstWhere('id', $classroomId)->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Mata Pelajaran</td>
                                <td>:</td>
                                <td>
                                    @if($subjectId)
                                        {{ $subjects->firstWhere('id', $subjectId)->name ?? '-' }}
                                    @else
                                        Semua Mata Pelajaran
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Periode</td>
                                <td>:</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }} s/d
                                    {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            @if($classroomId && $attendances->isNotEmpty())
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>
                                <th class="text-center">Hadir</th>
                                <th class="text-center">Izin</th>
                                <th class="text-center">Sakit</th>
                                <th class="text-center">Alpha</th>
                                <th class="text-center">Total %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $studentId => $studentAtts)
                                @php
                                    $student = $studentAtts->first()->student;
                                    $hadir = $studentAtts->where('status', 'Hadir')->count();
                                    $izin = $studentAtts->where('status', 'Izin')->count();
                                    $sakit = $studentAtts->where('status', 'Sakit')->count();
                                    $alpha = $studentAtts->where('status', 'Alpha')->count();
                                    $total = $studentAtts->count();
                                    $percentage = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td>{{ $student->name }}</td>
                                    <td class="text-center">{{ $hadir }}</td>
                                    <td class="text-center">{{ $izin }}</td>
                                    <td class="text-center">{{ $sakit }}</td>
                                    <td class="text-center">{{ $alpha }}</td>
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

            @elseif($classroomId)
                <div class="alert alert-info">Tidak ada data absensi untuk filter ini.</div>
            @else
                <div class="alert alert-warning">Silakan pilih kelas terlebih dahulu.</div>
            @endif

            <div class="mt-4 print-only">
                <p class="text-muted text-end">Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y HH:mm') }}
                </p>
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