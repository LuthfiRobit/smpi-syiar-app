@extends('layouts.admin')

@section('title', 'Monitoring Kelengkapan Perangkat Ajar')
@section('page-title', 'Monitoring Perangkat Ajar')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Monitoring Kelengkapan Perangkat Ajar</h5>
            <div class="d-flex align-items-center">
                <select class="form-select form-select-sm me-2" id="filterYear" style="width: 200px;">
                    @foreach(\App\Models\AcademicYear::orderBy('id', 'desc')->get() as $year)
                        <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>
                            {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
                <select class="form-select form-select-sm" id="filterTeacher" style="width: 200px;">
                    <option value="">Semua Guru</option>
                    @foreach(\App\Models\Teacher::orderBy('name')->get() as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Guru</th>
                        <th>NIP</th>
                        <th width="15%">Total Upload</th>
                        <th width="15%">Kelengkapan</th>
                        <th width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableMonitoring">
                    <tr>
                        <td colspan="6" class="text-center">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">
                        <i class="bx bx-detail me-2"></i>Detail Perangkat Ajar - <span id="teacherName"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detailContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Memuat detail...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal Preview PDF (Flipbook) -->
    <div class="modal fade" id="modalPreviewPdf" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0 bg-dark text-white">
                    <h5 class="modal-title">
                        <i class="bx bx-book-open me-2"></i>Preview Buku PDF
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 position-relative">
                    <!-- Loading State -->
                    <div id="pdfLoader" class="position-absolute top-50 start-50 translate-middle text-center">
                        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-light mt-3">Memuat buku...</p>
                    </div>
                    <div id="flipbookContainer" class="w-100 h-100" style="min-height: 80vh;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Preview Drive -->
    <div class="modal fade" id="modalPreviewDrive" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header border-0 bg-gradient-primary text-white">
                    <h5 class="modal-title">
                        <i class="bx bxl-google-cloud me-2"></i>Preview Google Drive
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 position-relative bg-light">
                    <!-- Loading State -->
                    <div id="driveLoader" class="position-absolute top-50 start-50 translate-middle text-center">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-3">Memuat dokumen dari Google Drive...</p>
                    </div>
                    <iframe id="driveFrame" src="" class="w-100 h-100"
                        style="min-height: 90vh; border: none; opacity: 0; transition: opacity 0.3s ease;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- DearFlip CSS -->
    <link href="https://cdn.jsdelivr.net/npm/dflip/css/dflip.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/dflip/css/themify-icons.min.css" rel="stylesheet">

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #696cff 0%, #5a5fc7 100%);
        }
    </style>
@endpush

@push('scripts')
    <!-- DearFlip JS -->
    <script src="https://cdn.jsdelivr.net/npm/dflip/js/libs/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dflip/js/dflip.min.js"></script>

    <script>
        $(document).ready(function () {
            let currentYearId = $('#filterYear').val();
            let currentTeacherId = '';

            function loadMonitoring() {
                $.get("{{ route('transactions.teaching-materials.admin-data') }}", {
                    year_id: currentYearId,
                    teacher_id: currentTeacherId
                }, function (data) {
                    let html = '';
                    if (data.teachers.length > 0) {
                        data.teachers.forEach(function (teacher, index) {
                            const totalTypes = data.types.length;
                            const filledTypes = teacher.materials_count;
                            const percentage = totalTypes > 0 ? Math.round((filledTypes / totalTypes) * 100) : 0;
                            const color = percentage == 100 ? 'success' : (percentage >= 50 ? 'warning' : 'danger');

                            html += `
                                            <tr>
                                                <td>${index + 1}</td>
                                                <td>
                                                    <strong>${teacher.name}</strong>
                                                </td>
                                                <td><span class="badge bg-label-secondary">${teacher.nip}</span></td>
                                                <td>
                                                    <span class="badge bg-label-info">
                                                        <i class="bx bx-file me-1"></i>${filledTypes} / ${totalTypes}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 25px;">
                                                        <div class="progress-bar bg-${color}" role="progressbar" 
                                                            style="width: ${percentage}%;" 
                                                            aria-valuenow="${percentage}" 
                                                            aria-valuemin="0" 
                                                            aria-valuemax="100">
                                                            <strong>${percentage}%</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary btn-detail" 
                                                        data-teacher-id="${teacher.id}" 
                                                        data-teacher-name="${teacher.name}">
                                                        <i class="bx bx-detail"></i> Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        `;
                        });
                    } else {
                        html = '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>';
                    }
                    $('#tableMonitoring').html(html);
                }).fail(function () {
                    Toast.fire({ icon: 'error', title: 'Gagal memuat data' });
                });
            }

            // Initial load
            loadMonitoring();

            // Filter change
            $('#filterYear, #filterTeacher').change(function () {
                currentYearId = $('#filterYear').val();
                currentTeacherId = $('#filterTeacher').val();
                loadMonitoring();
            });

            // Detail button
            $(document).on('click', '.btn-detail', function () {
                const teacherId = $(this).data('teacher-id');
                const teacherName = $(this).data('teacher-name');

                $('#teacherName').text(teacherName);
                $('#modalDetail').modal('show');

                // Load detail
                $.get("{{ route('transactions.teaching-materials.admin-detail') }}", {
                    teacher_id: teacherId,
                    year_id: currentYearId
                }, function (data) {
                    let html = '<div class="row g-3">';

                    data.types.forEach(function (type) {
                        const material = data.materials.find(m => m.teaching_material_type_id == type.id);

                        html += `
                                        <div class="col-md-6">
                                            <div class="card ${material ? 'border-success' : 'border-danger'}">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="mb-0">
                                                            <i class="bx bx-folder-open me-1"></i>${type.name}
                                                        </h6>
                                                        ${material ?
                                '<span class="badge bg-success"><i class="bx bx-check-circle"></i> Sudah Upload</span>' :
                                '<span class="badge bg-danger"><i class="bx bx-x-circle"></i> Belum Upload</span>'
                            }
                                                    </div>
                                                    ${type.description ? `<p class="text-muted small mb-2">${type.description}</p>` : ''}
                                                    ${material ? `
                                                        <div class="mt-2">
                                                            <p class="mb-1"><strong>Deskripsi:</strong> ${material.description}</p>
                                                            <p class="mb-1">
                                                                <span class="badge bg-label-primary">Kelas ${material.grade_level}</span>
                                                                ${material.subject ? `<span class="badge bg-label-info">${material.subject.name}</span>` : ''}
                                                            </p>
                                                            <div class="mt-2">
                                                                ${material.file_type == 'file' ?
                                    `<button onclick="previewPdf('/storage/${material.file_path}')" class="btn btn-sm btn-outline-info">
                                                                        <i class="bx bx-book-open"></i> Buka Buku
                                                                    </button>` :
                                    `<button onclick="previewDrive('${material.link_url}')" class="btn btn-sm btn-outline-warning">
                                                                        <i class="bx bxl-google-cloud"></i> Preview Drive
                                                                    </button>`
                                }
                                                            </div>
                                                        </div>
                                                    ` : '<p class="text-muted small mb-0">Belum ada upload untuk jenis ini</p>'}
                                                </div>
                                            </div>
                                        </div>
                                    `;
                    });

                    html += '</div>';
                    $('#detailContent').html(html);
                }).fail(function () {
                    $('#detailContent').html('<div class="alert alert-danger">Gagal memuat detail</div>');
                });
            });
        });

        // Preview Functions with Loading States (Global Scope)
        function previewPdf(url) {
            // Show loader
            $('#pdfLoader').show();
            $('#flipbookContainer').html('').hide();

            // Show modal
            const pdfModal = new bootstrap.Modal(document.getElementById('modalPreviewPdf'));
            pdfModal.show();

            // Initialize DearFlip with delay to ensure modal is visible
            setTimeout(() => {
                try {
                    const options = {
                        source: url,
                        backgroundColor: "#222",
                        height: "100%",
                        duration: 700,
                        onReady: function () {
                            // Hide loader and show flipbook with fade-in
                            $('#pdfLoader').fadeOut(300, function () {
                                $('#flipbookContainer').fadeIn(500);
                            });
                        }
                    };
                    $("#flipbookContainer").flipBook(url, options);
                } catch (error) {
                    console.error('Error loading PDF:', error);
                    $('#pdfLoader').html('<div class="text-light"><i class="bx bx-error-circle fs-1"></i><p class="mt-3">Gagal memuat PDF</p></div>');
                }
            }, 300);
        }

        function previewDrive(url) {
            const iframe = document.getElementById('driveFrame');
            const loader = document.getElementById('driveLoader');

            // Reset loader content
            loader.innerHTML = `
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-3">Memuat dokumen dari Google Drive...</p>
                `;

            // Show loader, hide iframe
            loader.style.display = 'block';
            iframe.style.opacity = '0';

            // Convert URL to preview format
            let previewUrl = url.replace(/\/view.*$/, '/preview').replace(/\/edit.*$/, '/preview');
            if (!previewUrl.includes('preview')) {
                if (url.includes('drive.google.com')) {
                    previewUrl = url.endsWith('/') ? url + 'preview' : url + '/preview';
                }
            }

            // Show modal
            const driveModal = new bootstrap.Modal(document.getElementById('modalPreviewDrive'));
            driveModal.show();

            // Handle iframe error
            iframe.onerror = function () {
                loader.innerHTML = `
                        <div class="text-danger text-center">
                            <i class="bx bx-error-circle" style="font-size: 4rem;"></i>
                            <p class="mt-3 fw-bold">Link Drive Belum Public</p>
                            <p class="text-muted small">Pastikan file di Google Drive sudah diatur ke "Anyone with the link can view"</p>
                            <a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="bx bx-link-external"></i> Buka di Tab Baru
                            </a>
                        </div>
                    `;
                loader.style.display = 'block';
                iframe.style.display = 'none';
            };

            // Set iframe source
            iframe.src = previewUrl;
            iframe.style.display = 'block';

            // Handle iframe load
            iframe.onload = function () {
                // Check if iframe content is accessible (might be blocked)
                setTimeout(() => {
                    try {
                        // Try to detect if content loaded properly
                        loader.style.display = 'none';
                        iframe.style.opacity = '1';
                    } catch (e) {
                        // Cross-origin error - show error message
                        loader.innerHTML = `
                                <div class="text-danger text-center">
                                    <i class="bx bx-error-circle" style="font-size: 4rem;"></i>
                                    <p class="mt-3 fw-bold">Link Drive Belum Public</p>
                                    <p class="text-muted small">Pastikan file di Google Drive sudah diatur ke "Anyone with the link can view"</p>
                                    <a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="bx bx-link-external"></i> Buka di Tab Baru
                                    </a>
                                </div>
                            `;
                        loader.style.display = 'block';
                        iframe.style.display = 'none';
                    }
                }, 500);
            };

            // Fallback timeout - if nothing loads after 8 seconds, show error
            setTimeout(() => {
                if (loader.style.display !== 'none' && !loader.innerHTML.includes('bx-error-circle')) {
                    loader.innerHTML = `
                            <div class="text-warning text-center">
                                <i class="bx bx-time-five" style="font-size: 4rem;"></i>
                                <p class="mt-3 fw-bold">Link Drive Belum Public atau Terlalu Lama</p>
                                <p class="text-muted small">File mungkin tidak tersedia atau link belum diatur public</p>
                                <a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bx bx-link-external"></i> Buka di Tab Baru
                                </a>
                            </div>
                        `;
                }
            }, 8000);
        }

        // Reset modals on close
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('modalPreviewPdf').addEventListener('hidden.bs.modal', function () {
                $('#flipbookContainer').html('');
                $('#pdfLoader').show();
            });

            document.getElementById('modalPreviewDrive').addEventListener('hidden.bs.modal', function () {
                const iframe = document.getElementById('driveFrame');
                iframe.src = '';
                iframe.style.opacity = '0';
                document.getElementById('driveLoader').style.display = 'block';
            });
        });
    </script>
@endpush