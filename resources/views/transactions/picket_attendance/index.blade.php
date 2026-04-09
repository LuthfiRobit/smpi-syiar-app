@extends('layouts.admin')

@section('title', 'Absensi Piket')
@section('page-title', 'Absensi Piket')

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom">
                    <h5 class="mb-0">Absensi Piket: <span
                            class="text-primary">{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</span></h5>
                    <span class="badge bg-label-info">{{ $teacher->name }}</span>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        @if($scheduled)
                            <div class="alert alert-primary mb-4">
                                <i class="bx bx-info-circle me-1"></i> Anda memiliki jadwal piket hari ini ({{ $dayName }}).
                            </div>
                        @else
                            <div class="alert alert-warning mb-4">
                                <i class="bx bx-error-circle me-1"></i> Anda tidak memiliki jadwal piket rutin hari ini.
                                <br><small>Anda tetap bisa melakukan absensi jika diminta piket tambahan.</small>
                            </div>
                        @endif

                        <h1 class="display-3 mb-4 text-primary fw-bold" id="liveClock">00:00:00</h1>

                        @if(!$attendance)
                            <form action="{{ route('transactions.picket-attendance.store') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-lg btn-success px-5 shadow-sm">
                                    <i class="bx bx-shield-check me-2"></i> MULAI PIKET
                                </button>
                            </form>
                        @else
                            <div class="mb-4">
                                <div class="row g-2 justify-content-center">
                                    <div class="col-auto">
                                        <div class="p-3 border rounded bg-light">
                                            <small class="text-muted d-block text-uppercase">Check-in</small>
                                            <span class="fs-4 fw-bold text-success">{{ $attendance->check_in }}</span>
                                        </div>
                                    </div>
                                    @if($attendance->check_out)
                                        <div class="col-auto">
                                            <div class="p-3 border rounded bg-light">
                                                <small class="text-muted d-block text-uppercase">Check-out</small>
                                                <span class="fs-4 fw-bold text-secondary">{{ $attendance->check_out }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if(!$attendance->check_out)
                                <form action="{{ route('transactions.picket-attendance.update', $attendance->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-lg btn-outline-danger px-5">
                                        <i class="bx bx-log-out-circle me-2"></i> SELESAI PIKET
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-success border-0 bg-lighter text-success fw-bold">
                                    <i class="bx bx-check-double me-1"></i> Anda telah menyelesaikan tugas piket hari ini.
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function updateClock() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('id-ID', { hour12: false });
                document.getElementById('liveClock').innerText = timeString;
            }
            setInterval(updateClock, 1000);
            updateClock();
        </script>
    @endpush
@endsection