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
                    @forelse($groupedSchedules as $group)
                        <tr>
                            <td>
                                <span class="fw-bold">{{ implode(' - ', $group['time_slot_names']) }}</span><br>
                                <span class="badge bg-label-secondary">
                                    {{ \Carbon\Carbon::parse($group['start_time'])->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($group['end_time'])->format('H:i') }}
                                </span>
                            </td>
                            <td>{{ $group['classroom_name'] }}</td>
                            <td>{{ $group['subject_name'] }}</td>
                            <td>
                                @if($group['is_filled'])
                                    @if($group['journal_status'] == 'Approved')
                                        <span class="badge bg-label-success">Disetujui</span>
                                    @elseif($group['journal_status'] == 'Submitted')
                                        <span class="badge bg-label-info">Menunggu Konfirmasi</span>
                                    @else
                                        <span class="badge bg-label-primary">Draft</span>
                                    @endif
                                @else
                                    <span class="badge bg-label-warning">Belum Diisi</span>
                                @endif
                            </td>
                            <td>
                                @if($group['is_filled'])
                                    @if($group['journal_status'] == 'Draft' || $group['journal_status'] == 'Submitted')
                                        <a href="{{ route('transactions.journals.edit', $group['journal_id']) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="bx bx-edit"></i> Edit
                                        </a>
                                    @else
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="bx bx-lock"></i> Terkunci
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('transactions.journals.create', implode(',', $group['schedule_ids'])) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="bx bx-pencil"></i> Isi Jurnal
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