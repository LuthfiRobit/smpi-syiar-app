@extends('layouts.admin')

@section('title', 'Master Jam Pelajaran')
@section('page-title', 'Master Jam Pelajaran')

@section('content')
<div class="card shadow-sm">
    <!-- Card Header -->
    <div class="card-header bg-white border-bottom">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <h5 class="mb-0 fw-semibold">Daftar Jam</h5>
            </div>

            <div class="col-12 col-md-8">
                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                    <select
                        id="filterDay"
                        class="form-select form-select-sm"
                        style="max-width: 160px;"
                    >
                        <option value="">Semua Hari</option>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>

                    <select
                        id="filterType"
                        class="form-select form-select-sm"
                        style="max-width: 160px;"
                    >
                        <option value="">Semua Tipe</option>
                        <option value="Pelajaran">Pelajaran</option>
                        <option value="Istirahat">Istirahat</option>
                    </select>

                    <button
                        class="btn btn-primary btn-sm"
                        id="btnTambahJam"
                    >
                        <i class="bx bx-plus me-1"></i>
                        Tambah Jam
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 120px;">Aksi</th>
                    <th>Hari</th>
                    <th>Jam Ke-</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Tipe</th>
                </tr>
            </thead>
            <tbody id="tableTimeSlot">
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Card Footer -->
    <div class="card-footer bg-white border-top py-2">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div id="paginationInfo" class="text-muted small"></div>

            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0" id="paginationLinks"></ul>
            </nav>
        </div>
    </div>
</div>


    <!-- Modal Jam -->
    <div class="modal fade" id="modalJam" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formTimeSlot">
                    @csrf
                    <input type="hidden" id="timeslot_id" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalJamTitle">Tambah Jam Pelajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Hari</label>

                            <!-- Single Select for Edit -->
                            <div id="daySelectContainer" style="display: none;">
                                <select class="form-select" name="day" id="day">
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                    <option value="Minggu">Minggu</option>
                                </select>
                            </div>

                            <!-- Bulk Checkboxes for Add -->
                            <div id="dayCheckboxesContainer">
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $d)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="days[]" value="{{ $d }}"
                                                id="day_{{ $d }}">
                                            <label class="form-check-label" for="day_{{ $d }}">
                                                {{ $d }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-text small text-muted">Centang hari yang ingin diterapkan pengaturan jam
                                    yang sama.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jam Ke- / Key (Angka / strip untuk istirahat)</label>
                            <input type="text" class="form-control" name="time_key" id="time_key"
                                placeholder="Contoh: 1 atau -" required>
                        </div>
                        <div class="row g-2">
                            <div class="col mb-3">
                                <label class="form-label">Waktu Mulai</label>
                                <input type="time" class="form-control" name="start_time" id="start_time" required>
                            </div>
                            <div class="col mb-3">
                                <label class="form-label">Waktu Selesai</label>
                                <input type="time" class="form-control" name="end_time" id="end_time" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipe</label>
                            <select class="form-select" name="type" id="type" required>
                                <option value="Pelajaran">Pelajaran</option>
                                <option value="Istirahat">Istirahat</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Jam</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            let currentPage = 1;

            function loadTimeSlots(page = 1) {
                currentPage = page;
                let day = $('#filterDay').val();
                let type = $('#filterType').val();

                $.get("{{ route('masters.time-slots.data') }}", {
                    page: page,
                    day: day,
                    type: type
                }, function (response) {
                    let data = response.data; // Access data for pagination
                    let html = '';

                    if (data.length > 0) {
                        data.forEach(function (item) {
                            let type = item.is_break ? 'Istirahat' : 'Pelajaran';
                            let badgeClass = item.is_break ? 'bg-label-warning' : 'bg-label-primary';
                            html += `
                                                <tr>
                                                    <td>
                                                        <button class="btn btn-sm btn-icon btn-outline-warning btn-edit-jam" data-item='${JSON.stringify(item)}'><i class="bx bx-edit"></i></button>
                                                        <button class="btn btn-sm btn-icon btn-outline-danger btn-delete-jam" data-id="${item.id}"><i class="bx bx-trash"></i></button>
                                                    </td>
                                                    <td>${item.day}</td>
                                                    <td>${item.name}</td>
                                                    <td>${item.start_time.substring(0, 5)}</td>
                                                    <td>${item.end_time.substring(0, 5)}</td>
                                                    <td><span class="badge ${badgeClass}">${type}</span></td>
                                                </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>';
                    }

                    $('#tableTimeSlot').html(html);
                    renderPagination(response);
                });
            }

            function renderPagination(response) {
                let paginationHtml = '';
                let current = response.current_page;
                let last = response.last_page;

                $('#paginationInfo').text(`Menampilkan ${response.from || 0} sampai ${response.to || 0} dari ${response.total} data`);

                if (last > 1) {
                    // Previous
                    paginationHtml += `<li class="page-item ${current === 1 ? 'disabled' : ''}">
                                                <a class="page-link" href="#" data-page="${current - 1}">Previous</a>
                                               </li>`;

                    // Simple logic: Show all pages if small, or limited range if large
                    // For simplicity, showing range around current
                    let start = Math.max(1, current - 2);
                    let end = Math.min(last, current + 2);

                    if (start > 1) {
                        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                        if (start > 2) paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }

                    for (let i = start; i <= end; i++) {
                        paginationHtml += `<li class="page-item ${i === current ? 'active' : ''}">
                                                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                                                   </li>`;
                    }

                    if (end < last) {
                        if (end < last - 1) paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                        paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${last}">${last}</a></li>`;
                    }

                    // Next
                    paginationHtml += `<li class="page-item ${current === last ? 'disabled' : ''}">
                                                <a class="page-link" href="#" data-page="${current + 1}">Next</a>
                                               </li>`;
                }

                $('#paginationLinks').html(paginationHtml);
            }

            // Filter Events
            $('#filterDay, #filterType').change(function () {
                loadTimeSlots(1);
            });

            // Pagination Click Event
            $(document).on('click', '.page-link', function (e) {
                e.preventDefault();
                let page = $(this).data('page');
                if (page) loadTimeSlots(page);
            });

            // Initial Load
            loadTimeSlots();

            $('#btnTambahJam').click(function () {
                $('#formTimeSlot')[0].reset();
                $('#timeslot_id').val('');
                $('#modalJamTitle').text('Tambah Jam Pelajaran (Bulk)');

                // Mode: Add (Bulk)
                $('#daySelectContainer').hide().find('select').prop('disabled', true);
                $('#dayCheckboxesContainer').show().find('input').prop('disabled', false);

                $('#modalJam').modal('show');
            });

            $('#formTimeSlot').submit(function (e) {
                e.preventDefault();
                let id = $('#timeslot_id').val();
                let url = id ? "{{ route('masters.time-slots.update', ':id') }}".replace(':id', id) : "{{ route('masters.time-slots.store') }}";

                // Validate at least one day checked if in Add Mode
                if (!id) {
                    if ($('#dayCheckboxesContainer input:checked').length === 0) {
                        Toast.fire({ icon: 'warning', title: 'Pilih minimal satu hari!' });
                        return;
                    }
                }

                $.post(url, $(this).serialize(), function (response) {
                    Toast.fire({ icon: 'success', title: response.message });
                    $('#modalJam').modal('hide');
                    loadTimeSlots(currentPage);
                }).fail(function (xhr) {
                    Toast.fire({ icon: 'error', title: xhr.responseJSON.message || 'Error simpan data' });
                });
            });

            $(document).on('click', '.btn-edit-jam', function () {
                let item = $(this).data('item');
                $('#timeslot_id').val(item.id);

                // Mode: Edit (Single)
                $('#daySelectContainer').show().find('select').prop('disabled', false).val(item.day);
                $('#dayCheckboxesContainer').hide().find('input').prop('disabled', true);

                $('#time_key').val(item.name);
                $('#start_time').val(item.start_time.substring(0, 5));
                $('#end_time').val(item.end_time.substring(0, 5));
                $('#type').val(item.is_break ? 'Istirahat' : 'Pelajaran');
                $('#modalJamTitle').text('Edit Jam Pelajaran');
                $('#modalJam').modal('show');
            });

            $(document).on('click', '.btn-delete-jam', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('masters.time-slots.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            success: function (response) {
                                Toast.fire({ icon: 'success', title: response.message });
                                loadTimeSlots(currentPage);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush