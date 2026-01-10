@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-umum" aria-controls="navs-pills-top-umum" aria-selected="true">
                        <i class="bx bx-cog me-1"></i> Umum
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-tahun" aria-controls="navs-pills-top-tahun" aria-selected="false">
                        <i class="bx bx-calendar me-1"></i> Tahun Ajaran
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-akademik" aria-controls="navs-pills-top-akademik"
                        aria-selected="false">
                        <i class="bx bx-calendar-event me-1"></i> Akademik
                    </button>
                </li>
            </ul>
            <div class="tab-content border-0 p-0 shadow-none">
                <!-- Tab Identitas Sekolah -->
                @include('masters.settings.tabs.general')

                <!-- Tab Tahun Ajaran -->
                @include('masters.settings.tabs.academic-year')

                <!-- Tab Pengaturan Akademik -->
                @include('masters.settings.tabs.academic')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    <script>
        $(document).ready(function () {
            // --- Identitas Sekolah ---
            function loadIdentity() {
                $.get("{{ route('masters.settings.identity-data') }}", function (data) {
                    if (data) {
                        $('#name').val(data.name);
                        $('#npsn').val(data.npsn);
                        $('#email').val(data.email);
                        $('#phone').val(data.phone);
                        $('#website').val(data.website);
                        $('#headmaster_name').val(data.headmaster_name);
                        $('#headmaster_nip').val(data.headmaster_nip);
                        $('#address').val(data.address);

                        if (data.logo_path) {
                            $('#logoPreview').attr('src', "{{ asset('storage') }}/" + data.logo_path);
                        }
                    }
                });
            }
            loadIdentity();

            // Preview Logo
            $('#upload').change(function () {
                const file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function (event) {
                        $('#logoPreview').attr('src', event.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            $('#formIdentity').submit(function (e) {
                e.preventDefault();
                let formData = new FormData(this);

                $.ajax({
                    url: "{{ route('masters.settings.identity-update') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        Toast.fire({ icon: 'success', title: response.message });
                        loadIdentity();
                    },
                    error: function (xhr) {
                        Toast.fire({ icon: 'error', title: xhr.responseJSON.message || 'Terjadi kesalahan' });
                    }
                });
            });

            // --- Tahun Ajaran ---
            function loadAcademicYears() {
                $.get("{{ route('masters.academic-years.data') }}", function (data) {
                    let html = '';
                    let activeAlert = '';
                    data.forEach(function (item) {
                        let statusBadge = item.is_active
                            ? '<span class="badge bg-label-success">Aktif</span>'
                            : '<span class="badge bg-label-secondary">Non-Aktif</span>';

                        if (item.is_active) {
                            activeAlert = `
                                        <div class="alert alert-warning mb-4">
                                            <h6 class="alert-heading fw-bold mb-1">Tahun Ajaran Aktif: ${item.name} ${item.semester}</h6>
                                            <p class="mb-0">Seluruh data jadwal dan absensi saat ini menggunakan periode ini.</p>
                                        </div>`;
                        }

                        html += `
                                    <tr>
                                        <td>${item.name}</td>
                                        <td>${item.semester}</td>
                                        <td>${statusBadge}</td>
                                        <td>
                                            ${!item.is_active ? `<button class="btn btn-sm btn-success btn-set-active" data-id="${item.id}">Aktifkan</button>` : ''}
                                            <button class="btn btn-sm btn-icon btn-outline-warning btn-edit-year" data-item='${JSON.stringify(item)}'><i class="bx bx-edit"></i></button>
                                            <button class="btn btn-sm btn-icon btn-outline-danger btn-delete-year" data-id="${item.id}"><i class="bx bx-trash"></i></button>
                                        </td>
                                    </tr>`;
                    });
                    $('#tableAcademicYear').html(html || '<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>');
                    $('#activeYearAlert').html(activeAlert);
                });
            }
            loadAcademicYears();

            $('#formAcademicYear').submit(function (e) {
                e.preventDefault();
                let id = $('#year_id').val();
                let url = id ? "{{ route('masters.academic-years.update', ':id') }}".replace(':id', id) : "{{ route('masters.academic-years.store') }}";

                $.post(url, $(this).serialize(), function (response) {
                    Toast.fire({ icon: 'success', title: response.message });
                    $('#formAcademicYear')[0].reset();
                    $('#year_id').val('');
                    $('#btnSubmitYear').text('Simpan');
                    loadAcademicYears();
                }).fail(function (xhr) {
                    Toast.fire({ icon: 'error', title: 'Periksa kembali inputan Anda' });
                });
            });

            $(document).on('click', '.btn-edit-year', function () {
                let item = $(this).data('item');
                $('#year_id').val(item.id);
                $('#year').val(item.name);
                $('#semester').val(item.semester);
                $('#btnSubmitYear').text('Update');
            });

            $(document).on('click', '.btn-delete-year', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#696cff',
                    cancelButtonColor: '#8592a3',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('masters.academic-years.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            success: function (response) {
                                Toast.fire({ icon: 'success', title: response.message });
                                loadAcademicYears();
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.btn-set-active', function () {
                let id = $(this).data('id');
                $.post("{{ route('masters.academic-years.set-active', ':id') }}".replace(':id', id), function (response) {
                    Toast.fire({ icon: 'success', title: response.message });
                    loadAcademicYears();
                });
            });

            // --- Akademik (Hari Aktif & Libur) ---
            function loadAcademicSettings() {
                let yearId = $('#selectAcademicYearSettings').val();
                
                // Set hidden inputs for both forms
                if (yearId) {
                    $('#settings_active_days_year_id').val(yearId);
                    $('#settings_holiday_year_id').val(yearId);
                } else {
                     // If no year selected, maybe empty? Or keep previous?
                }

                $.get("{{ route('masters.settings.academic-data') }}?year_id=" + yearId, function (data) {
                    // Reset Active Days Checkboxes
                    $('.active-day-check').prop('checked', false);
                    
                    if (data.active_days && Array.isArray(data.active_days)) {
                        data.active_days.forEach(day => {
                            $(`.active-day-check[value="${day}"]`).prop('checked', true);
                        });
                    }

                    // Load Holidays
                    let html = '';
                    if (data.holidays && data.holidays.length > 0) {
                        data.holidays.forEach(h => {
                            let dateStr = h.is_recurring ?
                                moment(h.start_date).format('DD MMMM') :
                                moment(h.start_date).format('DD MMM YYYY') + (h.end_date !== h.start_date ? ' - ' + moment(h.end_date).format('DD MMM YYYY') : '');

                            html += `
                                <tr>
                                    <td>${h.name}</td>
                                    <td>${dateStr}</td>
                                    <td>${h.is_recurring ? '<span class="badge bg-label-info">Tahunan</span>' : '<span class="badge bg-label-secondary">Sekali</span>'}</td>
                                    <td>
                                        <button class="btn btn-sm btn-icon btn-outline-danger btn-delete-holiday" data-id="${h.id}"><i class="bx bx-trash"></i></button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        html = '<tr><td colspan="4" class="text-center">Belum ada data libur di tahun ini</td></tr>';
                    }
                    $('#tableHolidays').html(html);
                }).fail(function() {
                    Toast.fire({ icon: 'error', title: 'Gagal memuat pengaturan akademik' });
                });
            }

            // Load on tab click
            $('button[data-bs-target="#navs-pills-top-akademik"]').on('shown.bs.tab', function () {
                loadAcademicSettings();
            });

            // Reload on year change
            $('#selectAcademicYearSettings').change(function () {
                loadAcademicSettings();
            });

            // Handle Active Days Submit
            $('#formActiveDays').submit(function (e) {
                e.preventDefault();
                $.post("{{ route('masters.settings.update-active-days') }}", $(this).serialize(), function (response) {
                    Toast.fire({ icon: 'success', title: response.message });
                }).fail(function (xhr) {
                    let msg = xhr.responseJSON?.message || 'Gagal update hari aktif';
                    if(xhr.responseJSON?.errors?.active_days) {
                         msg = xhr.responseJSON.errors.active_days[0];
                    }
                    Toast.fire({ icon: 'error', title: msg });
                });
            });

            // Handle Holiday Submit
            $('#formHoliday').submit(function (e) {
                e.preventDefault();
                $.post("{{ route('masters.settings.holidays.store') }}", $(this).serialize(), function (response) {
                    Toast.fire({ icon: 'success', title: response.message });
                    $('#formHoliday')[0].reset();
                    // Restore hidden input value after reset
                    $('#settings_holiday_year_id').val($('#selectAcademicYearSettings').val());
                    
                    loadAcademicSettings();
                }).fail(function (xhr) {
                    Toast.fire({ icon: 'error', title: xhr.responseJSON.message || 'Error simpan data' });
                });
            });

            $(document).on('click', '.btn-delete-holiday', function () {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus data?',
                    text: "Data libur akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('masters.settings.holidays.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            success: function (response) {
                                Toast.fire({ icon: 'success', title: response.message });
                                loadAcademicSettings();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush