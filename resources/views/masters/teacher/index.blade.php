@extends('layouts.admin')

@section('title', 'Master Guru')
@section('page-title', 'Master Guru')

@section('content')
    <div class="card shadow-sm">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <h5 class="mb-0 fw-semibold">Daftar Guru</h5>
                </div>
                <div class="col-12 col-md-6">
                    <div class="d-flex flex-wrap justify-content-md-end gap-2">
                        <select class="form-select form-select-sm" id="filterStatus" style="max-width: 160px;">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>

                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
                            <i class="bx bx-upload me-1"></i>
                            Import Excel
                        </button>

                        <button class="btn btn-primary btn-sm" id="btnTambahGuru">
                            <i class="bx bx-plus me-1"></i>
                            Tambah Guru
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
                        <th style="width: 140px;">Aksi</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Status User</th>
                    </tr>
                </thead>
                <tbody id="tableTeacher">
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
            <div class="d-flex justify-content-end">
                <div id="paginationTeacher"></div>
            </div>
        </div>
    </div>


    <!-- Modal Guru -->
    <div class="modal fade" id="modalGuru" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formTeacher">
                    @csrf
                    <input type="hidden" id="teacher_id" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTeacherTitle">Tambah Guru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nip" id="nip"
                                    placeholder="Nomor Induk Pegawai" required>
                                <small class="text-muted">NIP akan digunakan sebagai password default</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Nama Lengkap"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="email"
                                    placeholder="email@example.com" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select class="form-select" name="gender" id="gender">
                                    <option value="">Pilih...</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="08xxxxxxxxxx">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="address" id="address" rows="2"></textarea>
                            </div>
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

    <!-- Modal Import -->
    <div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formImport" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Guru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Format CSV:</strong><br>
                            NIP, Nama, Email, Telepon, Alamat, Jenis Kelamin (L/P)<br>
                            <small>Contoh: 123456,Budi Santoso,budi@example.com,08123456789,Jl. Contoh,L</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">File CSV</label>
                            <input type="file" class="form-control" name="file" id="file" accept=".csv" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            function loadTeachers(page = 1) {
                let status = $('#filterStatus').val();
                $.get("{{ route('masters.teachers.data') }}", {
                    page: page,
                    status: status
                }, function (response) {
                    let html = '';
                    let data = response.data;

                    data.forEach(function (item) {
                        let statusBadge = item.user && item.user.is_active
                            ? '<span class="badge bg-label-success">Aktif</span>'
                            : '<span class="badge bg-label-secondary">Nonaktif</span>';

                        html += `
                                <tr>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-icon btn-outline-warning btn-edit-teacher" data-item='${JSON.stringify(item)}'><i class="bx bx-edit"></i></button>
                                            <button class="btn btn-sm btn-icon btn-outline-info btn-reset-teacher" data-id="${item.id}" title="Reset Password & Username"><i class="bx bx-refresh"></i></button>
                                            <button class="btn btn-sm btn-icon btn-outline-danger btn-delete-teacher" data-id="${item.id}"><i class="bx bx-trash"></i></button>
                                        </div>
                                    </td>
                                    <td><strong>${item.nip}</strong></td>
                                    <td>${item.name}</td>
                                    <td>${item.user ? item.user.email : '-'}</td>
                                    <td>${item.phone || '-'}</td>
                                    <td>${statusBadge}</td>
                                </tr>`;
                    });
                    $('#tableTeacher').html(html || '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>');

                    renderPagination(response, '#paginationTeacher', loadTeachers);
                });
            }

            loadTeachers();

            $('#filterStatus').change(function () {
                loadTeachers(1);
            });

            function renderPagination(response, containerId, callback) {
                let html = '<nav aria-label="Page navigation"><ul class="pagination pagination-sm justify-content-end mb-0">';

                html += `<li class="page-item ${response.prev_page_url ? '' : 'disabled'}">
                                <a class="page-link" href="javascript:void(0)" data-page="${response.current_page - 1}"><i class="tf-icon bx bx-chevrons-left"></i></a>
                             </li>`;

                let startPage = Math.max(1, response.current_page - 2);
                let endPage = Math.min(response.last_page, response.current_page + 2);

                for (let i = startPage; i <= endPage; i++) {
                    html += `<li class="page-item ${i === response.current_page ? 'active' : ''}">
                                    <a class="page-link" href="javascript:void(0)" data-page="${i}">${i}</a>
                                 </li>`;
                }

                html += `<li class="page-item ${response.next_page_url ? '' : 'disabled'}">
                                <a class="page-link" href="javascript:void(0)" data-page="${response.current_page + 1}"><i class="tf-icon bx bx-chevrons-right"></i></a>
                             </li>`;

                html += '</ul></nav>';
                $(containerId).html(html);

                $(containerId + ' .page-link').unbind().click(function (e) {
                    e.preventDefault();
                    let page = $(this).data('page');
                    if (page && page > 0 && page <= response.last_page && page !== response.current_page) {
                        callback(page);
                    }
                });
            }

            $('#btnTambahGuru').click(function () {
                $('#formTeacher')[0].reset();
                $('#teacher_id').val('');
                $('#modalTeacherTitle').text('Tambah Guru');
                $('#modalGuru').modal('show');
            });

            $('#formTeacher').submit(function (e) {
                e.preventDefault();
                let id = $('#teacher_id').val();
                let url = id ? "{{ route('masters.teachers.update', ':id') }}".replace(':id', id) : "{{ route('masters.teachers.store') }}";

                $.post(url, $(this).serialize(), function (response) {
                    Toast.fire({ icon: 'success', title: response.message });
                    $('#modalGuru').modal('hide');
                    loadTeachers();
                }).fail(function (xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        Toast.fire({ icon: 'error', title: errors });
                    } else {
                        Toast.fire({ icon: 'error', title: xhr.responseJSON.message || 'Error simpan data' });
                    }
                });
            });

            $(document).on('click', '.btn-edit-teacher', function () {
                let item = $(this).data('item');
                $('#teacher_id').val(item.id);
                $('#nip').val(item.nip);
                $('#name').val(item.name);
                $('#email').val(item.user ? item.user.email : '');
                $('#gender').val(item.gender);
                $('#phone').val(item.phone);
                $('#address').val(item.address);
                $('#modalTeacherTitle').text('Edit Guru');
                $('#modalGuru').modal('show');
            });

            $(document).on('click', '.btn-reset-teacher', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Reset Password & Username?',
                    text: "Password dan Username akan dikembalikan ke NIP guru!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, reset!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ route('masters.teachers.reset-password', ':id') }}".replace(':id', id), {
                            _token: "{{ csrf_token() }}"
                        }, function (response) {
                            Toast.fire({ icon: 'success', title: response.message });
                        }).fail(function (xhr) {
                            Toast.fire({ icon: 'error', title: xhr.responseJSON.message || 'Gagal reset data' });
                        });
                    }
                });
            });

            $(document).on('click', '.btn-delete-teacher', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data guru?',
                    text: "Akun user terkait juga akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('masters.teachers.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            success: function (response) {
                                Toast.fire({ icon: 'success', title: response.message });
                                loadTeachers();
                            }
                        });
                    }
                });
            });

            $('#formImport').submit(function (e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('masters.teachers.import') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $('#modalImport').modal('hide');
                        $('#formImport')[0].reset();

                        let message = response.message;
                        if (response.results && response.results.errors.length > 0) {
                            message += '<br><small>' + response.results.errors.slice(0, 5).join('<br>') + '</small>';
                        }

                        Swal.fire({
                            title: 'Import Selesai',
                            html: message,
                            icon: response.results.failed > 0 ? 'warning' : 'success'
                        });
                        loadTeachers();
                    },
                    error: function (xhr) {
                        Toast.fire({ icon: 'error', title: xhr.responseJSON.message || 'Error import data' });
                    }
                });
            });
        });
    </script>
@endpush