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
                <span class="badge bg-label-primary">{{ count($groupedJournals) }} Jurnal</span>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">Aksi</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Guru</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Topik</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupedJournals as $group)
                        <tr>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('transactions.journals.show', $group['id']) }}">
                                            <i class="bx bx-show-alt me-1"></i> Detail
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <h6 class="dropdown-header">Ubah Status (Sesi Ini)</h6>
                                        <form action="{{ route('transactions.journals.update-status', $group['id']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" name="status" value="Approved" class="dropdown-item text-success">
                                                <i class="bx bx-check-circle me-1"></i> Set Approved
                                            </button>
                                        </form>
                                        <form action="{{ route('transactions.journals.update-status', $group['id']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" name="status" value="Submitted" class="dropdown-item text-info">
                                                <i class="bx bx-send me-1"></i> Set Submitted
                                            </button>
                                        </form>
                                        <form action="{{ route('transactions.journals.update-status', $group['id']) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" name="status" value="Draft" class="dropdown-item text-secondary">
                                                <i class="bx bx-pencil me-1"></i> Set Draft
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold">{{ implode(' - ', $group['time_slot_names']) }}</span><br>
                                <span class="badge bg-label-secondary">
                                    {{ \Carbon\Carbon::parse($group['start_time'])->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($group['end_time'])->format('H:i') }}
                                </span>
                            </td>
                            <td>
                                @if($group['status'] == 'Approved')
                                    <span class="badge bg-label-success">Approved</span>
                                @elseif($group['status'] == 'Submitted')
                                    <span class="badge bg-label-info">Submitted</span>
                                @else
                                    <span class="badge bg-label-secondary">Draft</span>
                                @endif
                            </td>
                            <td><strong>{{ $group['teacher_name'] }}</strong></td>
                            <td>{{ $group['classroom_name'] }}</td>
                            <td>{{ $group['subject_name'] }}</td>
                            <td>{{ Str::limit($group['topic'], 30) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data jurnal untuk tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection