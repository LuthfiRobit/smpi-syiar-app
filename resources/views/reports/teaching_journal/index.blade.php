@extends('layouts.admin')

@section('title', 'Laporan Jurnal Mengajar')
@section('page-title', 'Laporan Jurnal Mengajar')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Laporan Jurnal Mengajar</h5>
            <button onclick="window.print()" class="btn btn-secondary btn-sm no-print">
                <i class="bx bx-printer me-1"></i> Print / PDF
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.teaching-journals') }}" method="GET"
                class="row g-3 mb-4 no-print border p-3 rounded">
                <div class="col-md-4">
                    <label class="form-label">Mulai Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
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
                <h4 class="text-center mb-4">LAPORAN JURNAL MENGAJAR</h4>
                <div class="row">
                    <div class="col-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="100">Periode</td>
                                <td width="10">:</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }} s/d
                                    {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            @if($journals->isNotEmpty())
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Guru</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Materi / Topik</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($journals as $journal)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($journal->date)->format('d/m/Y') }}</td>
                                    <td>{{ $journal->schedule->teacher->name }}</td>
                                    <td><span class="badge bg-label-primary">{{ $journal->schedule->classroom->name }}</span>
                                    </td>
                                    <td>{{ $journal->schedule->subject->name }}</td>
                                    <td>
                                        <strong>{{ $journal->topic }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($journal->description, 50) }}</small>
                                    </td>
                                    <td>
                                        @if($journal->status == 'Selesai')
                                            <span class="badge bg-label-success">Selesai</span>
                                        @else
                                            <span class="badge bg-label-warning">{{ $journal->status }}</span>
                                        @endif
                                    </td>
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

            @else
                <div class="alert alert-info">Tidak ada data jurnal pada rentang tanggal ini.</div>
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