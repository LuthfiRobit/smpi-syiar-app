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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Kehadiran: <span
                    class="text-primary">{{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}</span></h5>
            @if(isset($teacher))
                <small class="text-muted">{{ $teacher->name }}</small>
            @endif
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-center p-5 border rounded bg-lighter">
                <div class="text-center">
                    <h2 class="mb-3">
                        @if($todayStatus)
                            @if($todayStatus->status == 'Hadir')
                                Sudah Check-in
                            @else
                                Status: <span class="text-primary">{{ $todayStatus->status }}</span>
                            @endif
                        @else
                            Belum Check-in
                        @endif
                    </h2>
                    <h1 class="display-3 mb-4 text-primary fw-bold" id="liveClock">00:00:00</h1>

                    @if(!$todayStatus)
                        <form action="{{ route('transactions.teacher-attendance.store') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-lg btn-success px-5">
                                <i class="bx bx-log-in-circle me-2"></i> CHECK-IN
                            </button>
                        </form>
                    @else
                        <div class="mb-4">
                            <span class="badge bg-label-success fs-5 p-3 me-2">Masuk: {{ $todayStatus->check_in }}</span>
                            @if($todayStatus->check_out)
                                <span class="badge bg-label-secondary fs-5 p-3">Pulang: {{ $todayStatus->check_out }}</span>
                            @endif
                        </div>

                        @if(!$todayStatus->check_out)
                            <form action="{{ route('transactions.teacher-attendance.update', $todayStatus->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-lg btn-outline-danger px-5">
                                    <i class="bx bx-log-out-circle me-2"></i> CHECK-OUT
                                </button>
                            </form>
                        @else
                            <div class="alert alert-info">Anda sudah menyelesaikan absensi hari ini. Terimakasih!</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour12: false });
            document.getElementById('liveClock').innerText = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
@endsection