@extends('layouts.admin')

@section('title', 'Manajemen Kelas')
@section('page-title', 'Manajemen Kelas')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Kelas</h5>
            @if(auth()->user()->role === 'admin')
                <button class="btn btn-primary btn-sm" id="btnTambahKelas">
                    <i class="bx bx-plus me-1"></i> Tambah Kelas
                </button>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Tahun Ajaran</th>
                        <th>Wali Kelas</th>
                        <th>Jumlah Siswa</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="tableClassroom"></tbody>
            </table>
        </div>
    </div>

    <!-- Modal Kelas -->
    @if(auth()->user()->role === 'admin')
        <div class="modal fade" id="modalKelas" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form id="formClassroom">
                        @csrf
                        <input type="hidden" id="classroom_id" name="id">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalClassroomTitle">Tambah Kelas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Contoh: VII A"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tingkat <span class="text-danger">*</span></label>
                                <select class="form-select" name="grade_level" id="grade_level" required>
                                    <option value="">Pilih Tingkat...</option>
                                    <option value="7">VII</option>
                                    <option value="8">VIII</option>
                                    <option value="9">IX</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                                <select class="form-select" name="academic_year_id" id="academic_year_id" required>
                                    <option value="">Pilih Tahun Ajaran...</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->name }} - {{ $year->semester }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Wali Kelas</label>
                                <select class="form-select" name="teacher_id" id="teacher_id">
                                    <option value="">Belum ditentukan</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Assign Siswa -->
        <div class="modal fade" id="modalAssignSiswa" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Kelola Siswa - <span id="classroomName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="unassignedStudents" class="form-label">Pilih Siswa yang Belum Terjadwal</label>
                            <select class="form-select" id="unassignedStudents" multiple>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-primary mb-3" id="btnAddStudents">
                                <i class="bx bx-plus"></i> Tambahkan ke Kelas
                            </button>
                        </div>
                        <hr>
                        <h6>Siswa di Kelas Ini</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>NIS</th>
                                        <th>Nama</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="currentStudentsList"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        let currentClassroomId = null;
        let teacherSelect, unassignedStudentsSelect;

        $(document).ready(function() {
            // Initialize Tom Select for teacher dropdown
            teacherSelect = new TomSelect('#teacher_id', {
                placeholder: 'Belum ditentukan',
                allowEmptyOption: true,
                create: false
            });

            function loadClassrooms() {
                $.get("{{ route('academic.classrooms.data') }}", function(data) {
                    let html = '';
                    data.forEach(function(item) {
                        let romanGrade = item.grade_level == 7 ? 'VII' : (item.grade_level == 8 ? 'VIII' : 'IX');
                        html += `
                            <tr>
                                <td><strong>${item.name}</strong></td>
                                <td>${romanGrade}</td>
                                <td>${item.academic_year ? item.academic_year.name + ' ' + item.academic_year.semester : '-'}</td>
                                <td>${item.teacher ? item.teacher.name : '-'}</td>
                                <td><span class="badge bg-label-primary">${item.students_count || 0} siswa</span></td>
                                <td>
                                    <button class="btn btn-sm btn-icon btn-outline-info btn-manage-students" data-id="${item.id}" data-name="${item.name}"><i class="bx bx-user"></i></button>
                                    <button class="btn btn-sm btn-icon btn-outline-warning btn-edit-classroom" data-item='${JSON.stringify(item)}'><i class="bx bx-edit"></i></button>
                                    <button class="btn btn-sm btn-icon btn-outline-danger btn-delete-classroom" data-id="${item.id}"><i class="bx bx-trash"></i></button>
                                </td>
                            </tr>`;
                    });
                    $('#tableClassroom').html(html || '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>');
                });
            }
            loadClassrooms();

            $('#btnTambahKelas').click(function() {
                $('#formClassroom')[0].reset();
                $('#classroom_id').val('');
                $('#modalClassroomTitle').text('Tambah Kelas');
                teacherSelect.clear();
                $('#modalKelas').modal('show');
            });

            $('#formClassroom').submit(function(e) {
                e.preventDefault();
                let id = $('#classroom_id').val();
                let url = id ? "{{ route('academic.classrooms.update', ':id') }}".replace(':id', id) : "{{ route('academic.classrooms.store') }}";

                $.post(url, $(this).serialize(), function(response) {
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                    $('#modalKelas').modal('hide');
                    loadClassrooms();
                }).fail(function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: xhr.responseJSON.message || 'Error simpan data'
                    });
                });
            });

            $(document).on('click', '.btn-edit-classroom', function() {
                let item = $(this).data('item');
                $('#classroom_id').val(item.id);
                $('#name').val(item.name);
                $('#grade_level').val(item.grade_level);
                $('#academic_year_id').val(item.academic_year_id);
                teacherSelect.setValue(item.teacher_id || '');
                $('#modalClassroomTitle').text('Edit Kelas');
                $('#modalKelas').modal('show');
            });

            $(document).on('click', '.btn-delete-classroom', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus kelas?',
                    text: "Kelas harus kosong (tanpa siswa dan jadwal) untuk dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('academic.classrooms.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            success: function(response) {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.message
                                });
                                loadClassrooms();
                            },
                            error: function(xhr) {
                                Toast.fire({
                                    icon: 'error',
                                    title: xhr.responseJSON.message
                                });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-manage-students', function() {
                currentClassroomId = $(this).data('id');
                $('#classroomName').text($(this).data('name'));
                loadAssignModal();
                $('#modalAssignSiswa').modal('show');
            });

            function loadAssignModal() {
                // Destroy Tom Select first if exists
                if (unassignedStudentsSelect) {
                    unassignedStudentsSelect.destroy();
                    unassignedStudentsSelect = null;
                }

                // Load unassigned students
                $.get("{{ route('academic.classrooms.unassigned-students') }}", function(data) {
                    // Clear and repopulate select options
                    let selectElement = document.getElementById('unassignedStudents');
                    selectElement.innerHTML = '';
                    
                    data.forEach(function(student) {
                        let option = new Option(student.nis + ' - ' + student.name, student.id);
                        selectElement.add(option);
                    });

                    // Initialize Tom Select with fresh data
                    unassignedStudentsSelect = new TomSelect('#unassignedStudents', {
                        plugins: ['remove_button'],
                        placeholder: 'Pilih siswa...',
                        create: false
                    });
                });

                // Load current students
                $.get("{{ route('academic.classrooms.students', ':id') }}".replace(':id', currentClassroomId), function(data) {
                    let html = '';
                    data.forEach(function(student) {
                        html += `
                            <tr>
                                <td>${student.nis}</td>
                                <td>${student.name}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger btn-remove-student" data-id="${student.id}">
                                        <i class="bx bx-x"></i> Keluarkan
                                    </button>
                                </td>
                            </tr>`;
                    });
                    $('#currentStudentsList').html(html || '<tr><td colspan="3" class="text-center">Belum ada siswa</td></tr>');
                });
            }

            $('#btnAddStudents').click(function() {
                let studentIds = unassignedStudentsSelect.getValue();
                if (!studentIds || studentIds.length === 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Pilih siswa terlebih dahulu'
                    });
                    return;
                }

                $.post("{{ route('academic.classrooms.assign-students', ':id') }}".replace(':id', currentClassroomId), {
                    student_ids: studentIds
                }, function(response) {
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                    loadAssignModal();
                    loadClassrooms();
                });
            });

            $(document).on('click', '.btn-remove-student', function() {
                let studentId = $(this).data('id');
                let studentRow = $(this).closest('tr');
                
                Swal.fire({
                    title: 'Keluarkan siswa dari kelas?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, keluarkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('academic.classrooms.remove-student', ['classroomId' => ':classroomId', 'studentId' => ':studentId']) }}"
                                .replace(':classroomId', currentClassroomId)
                                .replace(':studentId', studentId),
                            type: 'DELETE',
                            success: function(response) {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.message
                                });
                                
                                // Fade out student row for better UX
                                studentRow.fadeOut(300, function() {
                                    // Reload both lists after animation
                                    loadAssignModal();
                                    loadClassrooms();
                                });
                            },
                            error: function(xhr) {
                                Toast.fire({
                                    icon: 'error',
                                    title: xhr.responseJSON?.message || 'Gagal mengeluarkan siswa'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush