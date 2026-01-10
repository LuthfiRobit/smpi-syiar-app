@extends('layouts.admin')

@section('title', 'Jurnal Mengajar')
@section('page-title', 'Jurnal Mengajar')

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Jadwal Mengajar Anda Hari Ini ({{ $todayName }}, {{ $todayDate }})</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Jam</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Status Jurnal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->timeSlot->start_time }} - {{ $schedule->timeSlot->end_time }}</td>
                            <td>{{ $schedule->classroom->name }}</td>
                            <td>{{ $schedule->subject->name }}</td>
                            <td>
                                @if($schedule->journal)
                                    <span class="badge bg-label-success">Sudah Diisi</span>
                                @else
                                    <span class="badge bg-label-warning">Belum Diisi</span>
                                @endif
                            </td>
                            <td>
                                @if($schedule->journal)
                                    <button class="btn btn-secondary btn-sm" disabled>Sudah Diisi</button>
                                @else
                                    <a href="{{ route('transactions.journals.create', $schedule->id) }}"
                                        class="btn btn-primary btn-sm">
                                        Isi Jurnal
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada jadwal mengajar hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection