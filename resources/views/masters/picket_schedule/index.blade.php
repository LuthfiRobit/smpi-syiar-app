@extends('layouts.admin')

@section('title', 'Jadwal Piket')
@section('page-title', 'Jadwal Piket')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <h5 class="mb-0 fw-semibold">Jadwal Piket Mingguan ({{ $activeYear->name }} -
                        {{ $activeYear->semester }})
                    </h5>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <button class="btn btn-primary btn-sm" id="btnTambahJadwal">
                        <i class="bx bx-plus me-1"></i>
                        Tambah Jadwal
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 120px;">Aksi</th>
                        <th style="width: 150px;">Hari</th>
                        <th>Nama Guru</th>
                        <th>NIP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($days as $day)
                        @php
                            $daySchedules = $schedules->where('day', $day);
                        @endphp
                        @if($daySchedules->count() > 0)
                            @foreach($daySchedules as $schedule)
                                <tr>
                                    <td>
                                        <form action="{{ route('masters.picket-schedules.destroy', $schedule->id) }}" method="POST"
                                            class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger btn-delete">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                    @if($loop->first)
                                        <td rowspan="{{ $daySchedules->count() }}" class="fw-bold text-primary">{{ $day }}</td>
                                    @endif
                                    <td><strong>{{ $schedule->teacher->name }}</strong></td>
                                    <td>{{ $schedule->teacher->nip ?? '-' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td></td>
                                <td class="fw-bold text-primary">{{ $day }}</td>
                                <td colspan="2" class="text-muted italic">Tidak ada petugas piket</td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada jadwal piket yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Jadwal -->
    <div class="modal fade" id="modalJadwal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('masters.picket-schedules.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="academic_year_id" value="{{ $activeYear->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Petugas Piket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Hari</label>
                            <select name="day" class="form-select" required>
                                <option value="">Pilih Hari</option>
                                @foreach($days as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guru Petugas</label>
                            <select name="teacher_id" class="form-select select2" required>
                                <option value="">Pilih Guru</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }} ({{ $teacher->nip ?? 'Tanpa NIP' }})
                                    </option>
                                @endforeach
                            </select>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Tom Select for teacher dropdown
            const teacherSelect = new TomSelect('select[name="teacher_id"]', {
                placeholder: 'Pilih Guru...',
                allowEmptyOption: true,
                create: false
            });

            $('#btnTambahJadwal').click(function () {
                teacherSelect.clear();
                $('#modalJadwal').modal('show');
            });

            $('.form-delete').submit(function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus jadwal?',
                    text: "Petugas piket akan dihapus dari hari ini.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endpush