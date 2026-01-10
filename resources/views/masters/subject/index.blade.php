@extends('layouts.admin')

@section('title', 'Master Mata Pelajaran')
@section('page-title', 'Master Mata Pelajaran')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Mapel</h5>
            <button class="btn btn-primary btn-sm" id="btnTambahMapel">
                <i class="bx bx-plus me-1"></i> Tambah Mapel
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableSubject">
                    <tr>
                        <td colspan="3" class="text-center">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Mapel -->
    <div class="modal fade" id="modalMapel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formSubject">
                    @csrf
                    <input type="hidden" id="subject_id" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSubjectTitle">Tambah Mata Pelajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Kode Mapel</label>
                            <input type="text" class="form-control" name="code" id="code" placeholder="Contoh: MTK-01"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Mapel</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Nama Mata Pelajaran"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Mapel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            function loadSubjects() {
                $.get("{{ route('masters.subjects.data') }}", function (data) {
                    let html = '';
                    data.forEach(function (item) {
                        html += `
                            <tr>
                                <td>${item.code}</td>
                                <td><strong>${item.name}</strong></td>
                                <td>
                                    <button class="btn btn-sm btn-icon btn-outline-warning btn-edit-subject" data-item='${JSON.stringify(item)}'><i class="bx bx-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-outline-danger btn-delete-subject" data-id="${item.id}"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>`;
                    });
                    $('#tableSubject').html(html || '<tr><td colspan="3" class="text-center">Tidak ada data</td></tr>');
                });
            }
            loadSubjects();

            $('#btnTambahMapel').click(function () {
                $('#formSubject')[0].reset();
                $('#subject_id').val('');
                $('#modalSubjectTitle').text('Tambah Mata Pelajaran');
                $('#modalMapel').modal('show');
            });

            $('#formSubject').submit(function (e) {
                e.preventDefault();
                let id = $('#subject_id').val();
                let url = id ? "{{ route('masters.subjects.update', ':id') }}".replace(':id', id) : "{{ route('masters.subjects.store') }}";

                $.post(url, $(this).serialize(), function (response) {
                    Toast.fire({ icon: 'success', title: response.message });
                    $('#modalMapel').modal('hide');
                    loadSubjects();
                }).fail(function (xhr) {
                    Toast.fire({ icon: 'error', title: xhr.responseJSON.message || 'Error simpan data' });
                });
            });

            $(document).on('click', '.btn-edit-subject', function () {
                let item = $(this).data('item');
                $('#subject_id').val(item.id);
                $('#code').val(item.code);
                $('#name').val(item.name);
                $('#modalSubjectTitle').text('Edit Mata Pelajaran');
                $('#modalMapel').modal('show');
            });

            $(document).on('click', '.btn-delete-subject', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('masters.subjects.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            success: function (response) {
                                Toast.fire({ icon: 'success', title: response.message });
                                loadSubjects();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush