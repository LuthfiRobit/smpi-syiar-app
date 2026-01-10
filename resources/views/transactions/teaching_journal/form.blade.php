@extends('layouts.admin')

@section('title', 'Input Jurnal Mengajar')
@section('page-title', 'Input Jurnal Mengajar')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="mb-0">Input Jurnal & Absensi Siswa</h5>
            <small class="text-muted">{{ $schedule->classroom->name }} - {{ $schedule->subject->name }}
                ({{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }})</small>
        </div>

        <form action="{{ route('transactions.journals.store') }}" method="POST">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

            <div class="card-body">
                <!-- Section Jurnal -->
                <h6 class="mb-3">1. Jurnal Mengajar</h6>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Materi / Topik Bahasan</label>
                        <input type="text" name="topic" class="form-control" placeholder="Contoh: Aljabar Linear" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Catatan / Deskripsi Kegiatan</label>
                        <textarea name="description" class="form-control" rows="1"
                            placeholder="Detail kegiatan pembelajaran..."></textarea>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Section Absensi Siswa -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">2. Absensi Siswa</h6>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="setAllHadir()">Set Semua
                        Hadir</button>
                </div>

                <div class="table-responsive text-nowrap border rounded">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">Nama Siswa</th>
                                <th width="40%">Status Kehadiran</th>
                                <th width="25%">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $student->name }} <br> <small class="text-muted">{{ $student->nis }}</small></td>
                                    <td>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]"
                                                id="h_{{ $student->id }}" value="Hadir" checked>
                                            <label class="btn btn-outline-success btn-sm"
                                                for="h_{{ $student->id }}">Hadir</label>

                                            <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]"
                                                id="s_{{ $student->id }}" value="Sakit">
                                            <label class="btn btn-outline-warning btn-sm"
                                                for="s_{{ $student->id }}">Sakit</label>

                                            <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]"
                                                id="i_{{ $student->id }}" value="Izin">
                                            <label class="btn btn-outline-info btn-sm" for="i_{{ $student->id }}">Izin</label>

                                            <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]"
                                                id="a_{{ $student->id }}" value="Alpha">
                                            <label class="btn btn-outline-danger btn-sm"
                                                for="a_{{ $student->id }}">Alpha</label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="note[{{ $student->id }}]" class="form-control form-control-sm"
                                            placeholder="Ket.">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer border-top d-flex justify-content-between">
                <a href="{{ route('transactions.journals.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Jurnal & Absensi</button>
            </div>
        </form>
    </div>


    <script>
        function setAllHadir() {
            document.querySelectorAll('input[value="Hadir"]').forEach(radio => radio.checked = true);
        }
    </script>
@endsection