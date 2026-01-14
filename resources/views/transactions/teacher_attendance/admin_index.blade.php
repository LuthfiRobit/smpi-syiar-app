@extends('layouts.admin')

@section('title', 'Absensi Guru')
@section('page-title', 'Absensi Guru')

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                <!-- Left: Title -->
                <div class="d-flex align-items-center gap-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        Absensi Guru
                    </h5>

                    <span class="badge bg-label-primary">
                        {{ count($teachers) }} Guru
                    </span>
                </div>

                <!-- Right: Filter -->
                <div class="d-flex align-items-center gap-2">
                    <input
                        type="date"
                        id="filterDate"
                        class="form-control form-control-sm"
                        value="{{ $today }}"
                        style="width: 180px;"
                    >
                </div>

            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">Aksi</th>
                        <th>Nama Guru</th>
                        <th>NIP</th>
                        <th>Status</th>
                        <th>Waktu Masuk</th>
                        <th>Waktu Pulang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $teacher)
                        @php
                            $attendance = $teacher->attendances->first();
                        @endphp
                        <tr>
                            <td>
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalAbsen{{ $teacher->id }}" title="Set Status">
                                        <i class="bx bx-edit-alt"></i>
                                    </button>

                                    @if($attendance && $attendance->check_in && !$attendance->check_out)
                                        <form action="{{ route('transactions.teacher-attendance.checkout', $attendance->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Checkout">
                                                <i class="bx bx-log-out"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <!-- Modal -->
                                <div class="modal fade" id="modalAbsen{{ $teacher->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Set Absensi: {{ $teacher->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('transactions.teacher-attendance.store') }}"
                                                    method="POST">
                                                    @csrf
                                                    <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                                                    <input type="hidden" name="date_filter"
                                                        id="dateFilterInput{{ $teacher->id }}" value="{{ $today }}">

                                                    <div class="d-grid gap-2">
                                                        <button type="submit" name="status" value="Hadir"
                                                            class="btn btn-success">Hadir (Check-in Sekarang)</button>
                                                        <button type="submit" name="status" value="Izin"
                                                            class="btn btn-info">Izin</button>
                                                        <button type="submit" name="status" value="Sakit"
                                                            class="btn btn-warning">Sakit</button>
                                                        <button type="submit" name="status" value="Alpha"
                                                            class="btn btn-danger">Alpha</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><strong>{{ $teacher->name }}</strong></td>
                            <td>{{ $teacher->nip }}</td>
                            <td>
                                @if($attendance)
                                    @if($attendance->status == 'Hadir')
                                        <span class="badge bg-label-success">Hadir</span>
                                    @elseif($attendance->status == 'Izin')
                                        <span class="badge bg-label-info">Izin</span>
                                    @elseif($attendance->status == 'Sakit')
                                        <span class="badge bg-label-warning">Sakit</span>
                                    @elseif($attendance->status == 'Alpha')
                                        <span class="badge bg-label-danger">Alpha</span>
                                    @endif
                                @else
                                    <span class="badge bg-label-secondary">Belum Absen</span>
                                @endif
                            </td>
                            <td>{{ $attendance && $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') : '-' }}
                            </td>
                            <td>{{ $attendance && $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        document.getElementById('filterDate').addEventListener('change', function () {
            const date = this.value;
            window.location.href = '{{ route("transactions.teacher-attendance.index") }}?date=' + date;
        });
    </script>
@endsection