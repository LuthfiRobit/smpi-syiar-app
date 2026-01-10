@extends('layouts.admin')

@section('title', 'Jadwal Pelajaran')
@section('page-title', 'Jadwal Pelajaran')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex gap-2 align-items-center">
                            <!-- Academic Year Filter -->
                            <div style="width: 250px;">
                                <select id="filterAcademicYear" class="form-control" autocomplete="off">
                                    <option value="">Pilih Tahun Ajaran...</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ ($activeYear && $activeYear->id == $year->id) ? 'selected' : '' }}>
                                            {{ $year->name }} ({{ $year->semester }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Classroom Filter -->
                            <div style="width: 250px;">
                                <select id="filterClassroom" class="form-control" placeholder="Pilih Kelas..."
                                    autocomplete="off">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($classrooms as $classroom)
                                        <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="btnMatrixView">
                                    <i class="bx bx-grid-alt"></i> Matrix
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btnListView">
                                    <i class="bx bx-list-ul"></i> List
                                </button>
                            </div>
                            @if(auth()->user()->role === 'admin')
                                <button type="button" class="btn btn-primary" id="btnTambahJadwal">
                                    <i class="bx bx-plus"></i> Tambah Jadwal
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Matrix View -->
                    <div id="matrixView" class="table-responsive">
                        <table class="table table-bordered table-striped" id="matrixTable">
                            <thead>
                                <tr id="matrixHeaderRow">
                                    <th class="text-center bg-light" style="width: 150px;">Jam</th>
                                    <!-- Dynamic Days Header -->
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center">Silakan pilih kelas terlebih dahulu</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- List View -->
                    <div id="listView" class="table-responsive" style="display: none;">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Hari</th>
                                    <th>Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Guru</th>
                                    @if(auth()->user()->role === 'admin')
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody id="listTable">
                                <tr>
                                    <td colspan="6" class="text-center">Silakan pilih kelas terlebih dahulu</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
        <!-- Modal Add/Edit Schedule -->
        <div class="modal fade" id="modalJadwal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalScheduleTitle">Tambah Jadwal Pelajaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formSchedule">
                        @csrf
                        <input type="hidden" name="id" id="schedule_id">

                        <div class="modal-body">
                            <!-- Cascading Selection -->
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tahun Ajaran</label>
                                    <select class="form-select" id="formAcademicYear" name="academic_year_id" required>
                                        <option value="">Pilih Tahun...</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}">{{ $year->name }} ({{ $year->semester }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Kelas</label>
                                    <select class="form-select" id="formClassroom" name="classroom_id" required>
                                        <option value="">Pilih Kelas...</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Hari</label>
                                    <select class="form-select" id="formDay" name="day" required>
                                        <option value="">Pilih Hari...</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Bulk Schedule Rows -->
                            <div id="bulkScheduleContainer">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Jam Pelajaran</th>
                                            <th>Mata Pelajaran</th>
                                            <th>Guru</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="scheduleRows">
                                        <!-- Rows added via JS -->
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="btnAddRow">
                                    <i class="bx bx-plus me-1"></i> Tambah Baris
                                </button>
                            </div>

                            <!-- Single edit container (for editing 1 slot) -->
                            <div id="singleScheduleContainer" style="display: none;">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Jam Pelajaran</label>
                                        <select class="form-select" name="time_slot_id" id="formTimeSlot">
                                            <option value="">Pilih Jam...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Mata Pelajaran</label>
                                        <select class="form-select" name="subject_id" id="formSubject">
                                            <option value="">Pilih Mapel...</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Guru</label>
                                        <select class="form-select" name="teacher_id" id="formTeacher">
                                            <option value="">Pilih Guru...</option>
                                            @foreach($teachers as $teacher)
                                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="availabilityAlert" class="mt-3"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script>
        let currentView = 'matrix';
        let filterAcademicYear, filterClassroom;
        let formAcademicYear, formClassroom, formDay, formTimeSlot, formSubject, formTeacher;
        let rowCount = 0;
        let tomSelectInstances = {};
        let activeDays = []; // Store active days for current context

        $(document).ready(function () {
            // Initialize Filter TomSelects
            filterAcademicYear = new TomSelect('#filterAcademicYear', { placeholder: 'Pilih Tahun Ajaran...' });
            filterClassroom = new TomSelect('#filterClassroom', {
                placeholder: 'Pilih Kelas...',
                allowEmptyOption: true,
                valueField: 'id',
                labelField: 'name',
                searchField: 'name'
            });

            // Initialize Modal TomSelects
            @if(auth()->user()->role === 'admin')
                formAcademicYear = new TomSelect('#formAcademicYear', { placeholder: 'Pilih Tahun...' });
                formClassroom = new TomSelect('#formClassroom', { placeholder: 'Pilih Kelas...', valueField: 'id', labelField: 'name', searchField: 'name' });
                formDay = new TomSelect('#formDay', { placeholder: 'Pilih Hari...' });

                // Single Edit
                formTimeSlot = new TomSelect('#formTimeSlot', {
                    placeholder: 'Pilih Jam...',
                    valueField: 'id', labelField: 'name', searchField: 'name',
                    render: {
                        option: function (data, escape) {
                            if (!data.start_time) return '<div>' + escape(data.name) + '</div>';
                            return '<div>' + escape(data.name) + ' (' + escape(data.start_time.substr(0, 5)) + ' - ' + escape(data.end_time.substr(0, 5)) + ')</div>';
                        },
                        item: function (data, escape) {
                            if (!data.start_time) return '<div>' + escape(data.name) + '</div>';
                            return '<div>' + escape(data.name) + ' (' + escape(data.start_time.substr(0, 5)) + ' - ' + escape(data.end_time.substr(0, 5)) + ')</div>';
                        }
                    }
                });
                formSubject = new TomSelect('#formSubject', { placeholder: 'Pilih Mapel...' });
                formTeacher = new TomSelect('#formTeacher', { placeholder: 'Pilih Guru...' });
            @endif

            // ========================
            // FILTER LOGIC
            // ========================

            // When Filter Academic Year changes
            filterAcademicYear.on('change', function (yearId) {
                if (!yearId) {
                    filterClassroom.clear();
                    filterClassroom.clearOptions();
                    resetMatrix();
                    return;
                }

                // Fetch Classrooms for this year
                $.get("{{ route('academic.schedules.classrooms') }}", { year_id: yearId }, function (data) {
                    filterClassroom.clear();
                    filterClassroom.clearOptions();
                    filterClassroom.addOption(data);
                });
            });

            // When Filter Classroom changes
            filterClassroom.on('change', function (classroomId) {
                if (!classroomId) {
                    resetMatrix();
                    return;
                }
                if (currentView === 'matrix') {
                    loadMatrix(classroomId);
                } else {
                    loadList(classroomId);
                }
            });

            // View Toggles
            $('#btnMatrixView').click(function () {
                currentView = 'matrix';
                $(this).addClass('active');
                $('#btnListView').removeClass('active');
                $('#matrixView').show(); $('#listView').hide();
                if (filterClassroom.getValue()) loadMatrix(filterClassroom.getValue());
            });

            $('#btnListView').click(function () {
                currentView = 'list';
                $(this).addClass('active');
                $('#btnMatrixView').removeClass('active');
                $('#listView').show(); $('#matrixView').hide();
                if (filterClassroom.getValue()) loadList(filterClassroom.getValue());
            });

            function resetMatrix() {
                $('#matrixHeaderRow').html('<th class="text-center bg-light">Jam</th>');
                $('#matrixTable tbody').html('<tr><td colspan="7" class="text-center">Silakan pilih kelas terlebih dahulu</td></tr>');
                $('#listTable').html('<tr><td colspan="6" class="text-center">Silakan pilih kelas terlebih dahulu</td></tr>');
            }

            // ========================
            // LOAD DATA LOGIC
            // ========================

            function loadMatrix(classroomId) {
                $('#matrixTable tbody').html('<tr><td colspan="10" class="text-center"><div class="spinner-border text-primary" role="status"></div> Memuat...</td></tr>');

                $.get(`{{ url('academic/schedules/matrix') }}/${classroomId}`, function (response) {
                    let data = response.matrix;
                    let days = response.active_days;
                    activeDays = days; // Store globally if needed

                    // Build Header
                    let headerHtml = '<th class="text-center bg-light" style="width: 150px;">Jam</th>';
                    days.forEach(day => {
                        headerHtml += `<th class="text-center bg-light">${day}</th>`;
                    });
                    $('#matrixHeaderRow').html(headerHtml);

                    // Build Body
                    let bodyHtml = '';
                    data.forEach(function (row) {
                        if (row.is_break) {
                            bodyHtml += `<tr class="table-secondary">
                                            <td class="text-center"><strong>${row.time_slot_name}</strong><br><small>${row.start_time.substr(0, 5)} - ${row.end_time.substr(0, 5)}</small></td>
                                            <td colspan="${days.length}" class="text-center fw-bold">ISTIRAHAT</td>
                                        </tr>`;
                        } else {
                            bodyHtml += `<tr>
                                            <td class="text-center"><strong>${row.time_slot_name}</strong><br><small>${row.start_time.substr(0, 5)} - ${row.end_time.substr(0, 5)}</small></td>`;

                            days.forEach(function (day) {
                                if (row[day] && row[day].id) {
                                    // Has Schedule
                                    bodyHtml += `<td class="schedule-cell p-2" data-id="${row[day].id}">
                                                        <div class="d-flex flex-column h-100 justify-content-between">
                                                            <div>
                                                                <div class="fw-bold text-primary mb-1">${row[day].subject}</div>
                                                                <div class="small text-muted"><i class="bx bx-user me-1"></i>${row[day].teacher}</div>
                                                            </div>
                                                            @if(auth()->user()->role === 'admin')
                                                                                                                    <div class="mt-2 pt-2 border-top d-flex gap-1">
                                                                                                                        <button class="btn btn-xs btn-outline-warning btn-edit-schedule" data-item='${JSON.stringify({
                                                                    id: row[day].id,
                                                                    classroom_id: classroomId,
                                                                    day: day,
                                                                    time_slot_id: row[day].time_slot_id,
                                                                    subject_id: row[day].subject_id,
                                                                    teacher_id: row[day].teacher_id,
                                                                    academic_year_id: row[day].academic_year_id ?? ""
                                                                })}'>
                                                                                                                            <i class="bx bx-edit"></i>
                                                                                                                        </button>
                                                                                                                        <button class="btn btn-xs btn-outline-danger btn-delete-schedule" data-id="${row[day].id}">
                                                                                                                            <i class="bx bx-trash"></i>
                                                                                                                        </button>
                                                                                                                    </div>
                                                            @endif
                                                        </div>
                                                    </td>`;
                                } else if (row[day] && row[day].empty) {
                                    // Slot Exists but Empty
                                    bodyHtml += '<td class="text-muted text-center vertical-middle" style="background-color: #f8f9fa;">-</td>';
                                } else {
                                    // Slot doesn't exist
                                    bodyHtml += '<td class="text-muted text-center vertical-middle" style="background-color: #eaeaea;">X</td>';
                                }
                            });
                            bodyHtml += '</tr>';
                        }
                    });
                    $('#matrixTable tbody').html(bodyHtml || '<tr><td colspan="7" class="text-center">Tidak ada jadwal</td></tr>');
                });
            }

            function loadList(classroomId) {
                let url = "{{ route('academic.schedules.data') }}?classroom_id=" + classroomId;
                $.get(url, function (data) {
                    let html = '';
                    data.forEach(function (item) {
                        html += `<tr>
                                        <td>${item.classroom.name}</td>
                                        <td>${item.day}</td>
                                        <td>${item.time_slot.name} (${item.time_slot.start_time.substr(0, 5)} - ${item.time_slot.end_time.substr(0, 5)})</td>
                                        <td>${item.subject.name}</td>
                                        <td>${item.teacher.name}</td>
                                        @if(auth()->user()->role === 'admin')
                                            <td>
                                                <button class="btn btn-sm btn-icon btn-warning btn-edit-schedule" data-item='${JSON.stringify(item)}'><i class="bx bx-edit"></i></button>
                                                <button class="btn btn-sm btn-icon btn-danger btn-delete-schedule" data-id="${item.id}"><i class="bx bx-trash"></i></button>
                                            </td>
                                        @endif
                                    </tr>`;
                    });
                    $('#listTable').html(html || '<tr><td colspan="6" class="text-center">Tidak ada jadwal</td></tr>');
                });
            }

            @if(auth()->user()->role === 'admin')

                // ========================
                // FORM LOGIC
                // ========================

                // Open Modal (Add)
                $('#btnTambahJadwal').click(function () {
                    resetForm();
                    $('#modalScheduleTitle').text('Tambah Jadwal Pelajaran (Bulk)');
                    $('#bulkScheduleContainer').show();
                    $('#singleScheduleContainer').hide();

                    // Pre-fill filter values if selected
                    let selectedYear = filterAcademicYear.getValue();
                    let selectedClass = filterClassroom.getValue();

                    if (selectedYear) {
                        formAcademicYear.setValue(selectedYear);
                    }

                    setTimeout(() => {
                        if (selectedClass) formClassroom.setValue(selectedClass);
                    }, 500);

                    // Add default rows
                    addRow();

                    $('#modalJadwal').modal('show');
                });

                // Add Row Button Listener
                $('#btnAddRow').click(function () {
                    addRow();
                });

                // Form Year Change -> Load Classrooms & Active Days & Reset Day/Class
                formAcademicYear.on('change', function (yearId) {
                    formClassroom.clear();
                    formClassroom.clearOptions();
                    formDay.clear();
                    formDay.clearOptions();

                    if (yearId) {
                        // Load Classrooms
                        $.get("{{ route('academic.schedules.classrooms') }}", { year_id: yearId }, function (data) {
                            formClassroom.addOption(data);
                        });

                        // Load Active Days
                        $.get("{{ route('academic.schedules.active-days') }}", { year_id: yearId }, function (days) {
                            let options = days.map(day => ({ value: day, text: day }));
                            formDay.addOption(options);
                        });
                    }
                });

                // Form Day Change -> Update Time Slots in Rows
                formDay.on('change', function (day) {
                    let yearId = formAcademicYear.getValue();
                    if (!day || !yearId) return;

                    $.get(`{{ url('academic/schedules/time-slots') }}/${day}?year_id=${yearId}`, function (data) {
                        // Update all row select options
                        $('.ts-time-slot').each(function () {
                            let idRaw = $(this).attr('id');
                            if (tomSelectInstances[idRaw]) {
                                tomSelectInstances[idRaw].clear();
                                tomSelectInstances[idRaw].clearOptions();
                                tomSelectInstances[idRaw].addOption(data);
                            }
                        });

                        // Also single edit select
                        formTimeSlot.clear();
                        formTimeSlot.clearOptions();
                        formTimeSlot.addOption(data);
                    });
                });

                function addRow(data = null) {
                    rowCount++;
                    let id = rowCount;
                    let html = `<tr id="row-${id}">
                                                <td>
                                                    <select class="form-select ts-time-slot" name="schedules[${id}][time_slot_id]" id="ts-${id}" required>
                                                        <option value="">Pilih Jam...</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select ts-subject" name="schedules[${id}][subject_id]" id="sj-${id}" required>
                                                        <option value="">Pilih Mapel...</option>
                                                        @foreach($subjects as $subject)
                                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select ts-teacher" name="schedules[${id}][teacher_id]" id="tc-${id}" required>
                                                        <option value="">Pilih Guru...</option>
                                                        @foreach($teachers as $teacher)
                                                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-remove-row" data-id="${id}">
                                                        <i class="bx bx-x"></i>
                                                    </button>
                                                </td>
                                            </tr>`;

                    $('#scheduleRows').append(html);

                    // Init TomSelects
                    let ts = new TomSelect(`#ts-${id}`, {
                        placeholder: 'Pilih Jam...', valueField: 'id', labelField: 'name', searchField: 'name',
                        render: {
                            option: function (data, escape) {
                                if (!data.start_time) return '<div>' + escape(data.name || '') + '</div>';
                                return '<div>' + escape(data.name) + ' (' + escape(data.start_time.substr(0, 5)) + ' - ' + escape(data.end_time.substr(0, 5)) + ')</div>';
                            },
                            item: function (data, escape) {
                                if (!data.start_time) return '<div>' + escape(data.name || '') + '</div>';
                                return '<div>' + escape(data.name) + ' (' + escape(data.start_time.substr(0, 5)) + ' - ' + escape(data.end_time.substr(0, 5)) + ')</div>';
                            }
                        }
                    });
                    tomSelectInstances[`ts-${id}`] = ts;
                    tomSelectInstances[`sj-${id}`] = new TomSelect(`#sj-${id}`, { placeholder: 'Pilih Mapel...' });
                    tomSelectInstances[`tc-${id}`] = new TomSelect(`#tc-${id}`, { placeholder: 'Pilih Guru...' });

                    // If day is already selected, fetch/populate options for this new row
                    let currentDay = formDay.getValue();
                    let currentYear = formAcademicYear.getValue();
                    if (currentDay && currentYear) {
                        $.get(`{{ url('academic/schedules/time-slots') }}/${currentDay}?year_id=${currentYear}`, function (data) {
                            ts.addOption(data);
                            if (data) ts.setValue(data.time_slot_id);
                        });
                    }
                }

                $(document).on('click', '.btn-remove-row', function () {
                    let id = $(this).data('id');
                    if (tomSelectInstances[`ts-${id}`]) tomSelectInstances[`ts-${id}`].destroy();
                    if (tomSelectInstances[`sj-${id}`]) tomSelectInstances[`sj-${id}`].destroy();
                    if (tomSelectInstances[`tc-${id}`]) tomSelectInstances[`tc-${id}`].destroy();
                    delete tomSelectInstances[`ts-${id}`];
                    delete tomSelectInstances[`sj-${id}`];
                    delete tomSelectInstances[`tc-${id}`];
                    $(`#row-${id}`).remove();
                });

                // Edit Schedule
                $(document).on('click', '.btn-edit-schedule', function () {
                    let item = $(this).data('item');
                    resetForm();

                    $('#schedule_id').val(item.id);
                    $('#modalScheduleTitle').text('Edit Jadwal Pelajaran');

                    $('#bulkScheduleContainer').hide();
                    $('#singleScheduleContainer').show();

                    // Set Cascading Values
                    formAcademicYear.setValue(item.academic_year_id);
                    // Need to wait for async loads or manually set options if we want speed
                    // For reliability, we trigger change and wait

                    // We must use a chain of promises or timeouts to ensure options exist before setting value
                    // Or simply add the single option we need manually

                    setTimeout(() => {
                        formClassroom.addOption({ id: item.classroom_id, name: 'Loading...' }); // Hacky, better to load real
                        formClassroom.setValue(item.classroom_id);

                        formDay.addOption({ value: item.day, text: item.day });
                        formDay.setValue(item.day);

                        // Time Slot
                        // We trigger Day Change to load actual slots, but also set current
                        $.get(`{{ url('academic/schedules/time-slots') }}/${item.day}?year_id=${item.academic_year_id}`, function (data) {
                            formTimeSlot.clearOptions();
                            formTimeSlot.addOption(data);
                            formTimeSlot.setValue(item.time_slot_id);
                        });

                    }, 500);

                    formSubject.setValue(item.subject_id);
                    formTeacher.setValue(item.teacher_id);

                    $('#modalJadwal').modal('show');
                });

                // Submit
                $('#formSchedule').submit(function (e) {
                    e.preventDefault();
                    let id = $('#schedule_id').val();
                    let url = id ? "{{ route('academic.schedules.update', ':id') }}".replace(':id', id) : "{{ route('academic.schedules.store') }}";

                    $.post(url, $(this).serialize(), function (response) {
                        $('#modalJadwal').modal('hide');
                        Toast.fire({ icon: 'success', title: response.message });
                        // Refresh view
                        let cid = filterClassroom.getValue();
                        if (cid) {
                            currentView === 'matrix' ? loadMatrix(cid) : loadList(cid);
                        }
                    }).fail(function (xhr) {
                        Toast.fire({ icon: 'error', title: xhr.responseJSON.message || 'Error saving data' });
                    });
                });

                // Delete
                $(document).on('click', '.btn-delete-schedule', function () {
                    let id = $(this).data('id');
                    Swal.fire({
                        title: 'Hapus jadwal?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, hapus!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('academic.schedules.destroy', ':id') }}".replace(':id', id),
                                type: 'DELETE',
                                success: function (response) {
                                    Toast.fire({ icon: 'success', title: response.message });
                                    let cid = filterClassroom.getValue();
                                    if (cid) currentView === 'matrix' ? loadMatrix(cid) : loadList(cid);
                                }
                            });
                        }
                    });
                });

                function resetForm() {
                    $('#formSchedule')[0].reset();
                    $('#schedule_id').val('');
                    $('#scheduleRows').empty();
                    rowCount = 0;
                    formAcademicYear.clear();
                    formClassroom.clear();
                    formDay.clear();
                    formSubject.clear();
                    formTeacher.clear();
                }

            @endif

            });
    </script>
@endpush