@extends('layouts.admin')

@section('title', 'Monitoring Absensi Piket')
@section('page-title', 'Monitoring Absensi Piket')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Kehadiran Piket: <span
                    class="text-primary">{{ \Carbon\Carbon::parse($today)->translatedFormat('l, d M Y') }}</span></h5>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalManualAttendance">
                <i class="bx bx-plus me-1"></i> Absen Manual
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Guru</th>
                        <th>Jadwal</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendances as $item)
                        @php
                            $teacher = $item['teacher'];
                            $att = $item['attendance'];
                            $status = $item['status'];
                            
                            $statusClass = [
                                'Hadir' => 'bg-success',
                                'Izin' => 'bg-warning',
                                'Sakit' => 'bg-info',
                                'Alpha' => 'bg-danger',
                                'Belum Hadir' => 'bg-secondary'
                            ][$status] ?? 'bg-secondary';
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $teacher->name }}</strong>
                                @if(!$item['is_scheduled'])
                                    <span class="badge bg-label-warning ms-1">Piket Tambahan</span>
                                @endif
                            </td>
                            <td>{{ $item['is_scheduled'] ? 'Sesuai Jadwal' : 'Piket Tambahan' }}</td>
                            <td><span class="badge bg-label-success text-dark">{{ $att->check_in ?? '-' }}</span></td>
                            <td><span class="badge bg-label-secondary text-dark">{{ $att->check_out ?? '-' }}</span></td>
                            <td>
                                <span class="badge {{ $statusClass }}">{{ $status }}</span>
                            </td>
                            <td><small class="text-muted">{{ $att->note ?? '-' }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data absensi untuk hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Manual Attendance -->
    <div class="modal fade" id="modalManualAttendance" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('transactions.picket-attendance.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Input Absensi Piket Manual</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Guru</label>
                            <select name="teacher_id" class="form-select" id="teacher_id_manual" required>
                                <option value="">Pilih Guru</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Hadir">Hadir</option>
                                <option value="Izin">Izin</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Alpha">Alpha</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan (Opsional)</label>
                            <textarea name="note" class="form-control" rows="2"
                                placeholder="Alasan izin/sakit atau catatan piket"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            $(document).ready(function () {
                // Initialize Tom Select for manual attendance
                const teacherSelect = new TomSelect('#teacher_id_manual', {
                    placeholder: 'Pilih Guru...',
                    allowEmptyOption: true,
                    create: false
                });

                // Reset select when modal opens
                $('#modalManualAttendance').on('show.bs.modal', function () {
                    teacherSelect.clear();
                });
            });
        </script>
    @endpush
@endsection