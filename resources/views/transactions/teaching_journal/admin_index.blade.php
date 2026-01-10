@extends('layouts.admin')

@section('title', 'Monitoring Jurnal Mengajar')
@section('page-title', 'Monitoring Jurnal Mengajar')

@section('content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Jurnal Masuk</h5>
            <div class="d-flex gap-2 align-items-center">
                <form action="{{ route('transactions.journals.admin-index') }}" method="GET"
                    class="d-flex align-items-center">
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}"
                        onchange="this.form.submit()" style="width: 180px;">
                </form>
                <span class="badge bg-label-primary">{{ count($journals) }} Jurnal</span>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">Aksi</th>
                        <th>Waktu</th>
                        <th>Guru</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Topik</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($journals as $journal)
                        <tr>
                            <td>
                                <a href="{{ route('transactions.journals.show', $journal->id) }}"
                                    class="btn btn-sm btn-icon btn-outline-info" data-bs-toggle="tooltip" title="Lihat Detail">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary">
                                    {{ \Carbon\Carbon::parse($journal->schedule->timeSlot->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($journal->schedule->timeSlot->end_time)->format('H:i') }}
                                </span>
                            </td>
                            <td><strong>{{ $journal->schedule->teacher->name }}</strong></td>
                            <td>{{ $journal->schedule->classroom->name }}</td>
                            <td>{{ $journal->schedule->subject->name }}</td>
                            <td>{{ Str::limit($journal->topic, 30) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data jurnal untuk tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection