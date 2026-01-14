@extends('layouts.admin')

@section('title', 'Master Jenis Perangkat Ajar')
@section('page-title', 'Master Jenis Perangkat Ajar')

@section('content')
    <div class="card shadow-sm">
        <!-- Card Header -->
        <div class="card-header bg-white border-bottom">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <h5 class="mb-0 fw-semibold">Daftar Jenis Perangkat Ajar</h5>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <button class="btn btn-primary btn-sm" id="btnTambahType">
                        <i class="bx bx-plus me-1"></i>
                        Tambah Jenis
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 120px;">Aksi</th>
                        <th style="width: 60px;">No</th>
                        <th>Nama Jenis</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody id="tableType">
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalType" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formType">
                    @csrf
                    <input type="hidden" id="type_id" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTypeTitle">Tambah Jenis Perangkat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Jenis <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Contoh: Modul Ajar"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea id="description" name="description" class="form-control" rows="3"
                                placeholder="Deskripsi singkat..."></textarea>
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
            // Load data function
            function loadTypes() {
                $.get("{{ route('masters.teaching-material-types.index') }}", function (data) {
                    let html = '';
                    if (data.length > 0) {
                        data.forEach(function (item, index) {
                            html += `
                                        <tr> 
                                            <td>
                                                <button class="btn btn-sm btn-icon btn-outline-warning btn-edit-type" data-item='${JSON.stringify(item)}'>
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-icon btn-outline-danger btn-delete-type" data-id="${item.id}">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </td>
                                            <td>${index + 1}</td>
                                            <td><strong>${item.name}</strong></td>
                                            <td>${item.description || '-'}</td>
                                        </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>';
                    }
                    $('#tableType').html(html);
                }).fail(function () {
                    Toast.fire({ icon: 'error', title: 'Gagal memuat data' });
                });
            }
            loadTypes();

            // Tambah button
            $('#btnTambahType').click(function () {
                $('#formType')[0].reset();
                $('#type_id').val('');
                $('#modalTypeTitle').text('Tambah Jenis Perangkat');
                $('#modalType').modal('show');
            });

            // Form submit (Create/Update)
            $('#formType').submit(function (e) {
                e.preventDefault();
                let id = $('#type_id').val();
                let url = id
                    ? "{{ route('masters.teaching-material-types.update', ':id') }}".replace(':id', id)
                    : "{{ route('masters.teaching-material-types.store') }}";

                let formData = $(this).serialize();
                // Add _method for PUT if updating
                if (id) {
                    formData += '&_method=PUT';
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        Toast.fire({ icon: 'success', title: response.message || 'Data berhasil disimpan' });
                        $('#modalType').modal('hide');
                        loadTypes();
                    },
                    error: function (xhr) {
                        let msg = xhr.responseJSON?.message || 'Gagal menyimpan data';
                        if (xhr.responseJSON?.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        }
                        Toast.fire({ icon: 'error', title: msg });
                    }
                });
            });

            // Edit button
            $(document).on('click', '.btn-edit-type', function () {
                let item = $(this).data('item');
                $('#type_id').val(item.id);
                $('#name').val(item.name);
                $('#description').val(item.description || '');
                $('#modalTypeTitle').text('Edit Jenis Perangkat');
                $('#modalType').modal('show');
            });

            // Delete button with SweetAlert
            $(document).on('click', '.btn-delete-type', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('masters.teaching-material-types.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (response) {
                                Toast.fire({ icon: 'success', title: response.message || 'Data berhasil dihapus' });
                                loadTypes();
                            },
                            error: function (xhr) {
                                Toast.fire({ icon: 'error', title: xhr.responseJSON?.message || 'Gagal menghapus data' });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush