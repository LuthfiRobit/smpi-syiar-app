@extends('layouts.admin')

@section('title', 'Detail Jurnal Mengajar')
@section('page-title', 'Detail Jurnal Mengajar')

@section('content')

    <div class="row">
        <!-- Informasi Jurnal -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header border-bottom">
                    <h5 class="mb-0">Informasi Jurnal</h5>
                </div>
                <div class="card-body mt-4">
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase small">Guru</label>
                        <div class="fw-bold">{{ $journal->schedule->teacher->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase small">Mata Pelajaran</label>
                        <div>{{ $journal->schedule->subject->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase small">Kelas</label>
                        <div>{{ $journal->schedule->classroom->name }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase small">Waktu</label>
                        <div>
                            {{ \Carbon\Carbon::parse($journal->date)->translatedFormat('l, d F Y') }} <br>
                            @foreach($siblingJournals as $s)
                                <span class="badge bg-label-primary">{{ $s->schedule->timeSlot->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="divider">
                        <div class="divider-text">Konten Jurnal</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase small">Topik / Bahasan</label>
                        <div class="fw-bold">{{ $journal->topic }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted text-uppercase small">Deskripsi Kegiatan</label>
                        <div class="text-wrap">{{ $journal->description ?? '-' }}</div>
                    </div>
                </div>
                <div class="card-footer border-top">
                    <a href="{{ route('transactions.journals.admin-index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Absensi Siswa -->
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Rekap Absensi Siswa</h5>
                    <div>
                        <span class="badge bg-label-success">H:
                            {{ $journal->studentAttendances->where('status', 'Hadir')->count() }}</span>
                        <span class="badge bg-label-warning">S:
                            {{ $journal->studentAttendances->where('status', 'Sakit')->count() }}</span>
                        <span class="badge bg-label-info">I:
                            {{ $journal->studentAttendances->where('status', 'Izin')->count() }}</span>
                        <span class="badge bg-label-danger">A:
                            {{ $journal->studentAttendances->where('status', 'Alpha')->count() }}</span>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Siswa</th>
                                <th>Status</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($journal->studentAttendances as $index => $attendance)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $attendance->student->name }} <br>
                                        <small class="text-muted">{{ $attendance->student->nis }}</small>
                                    </td>
                                    <td>
                                        @if($attendance->status == 'Hadir')
                                            <span class="badge bg-label-success">Hadir</span>
                                        @elseif($attendance->status == 'Izin')
                                            <span class="badge bg-label-info">Izin</span>
                                        @elseif($attendance->status == 'Sakit')
                                            <span class="badge bg-label-warning">Sakit</span>
                                        @elseif($attendance->status == 'Alpha')
                                            <span class="badge bg-label-danger">Alpha</span>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->note ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection