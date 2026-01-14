@extends('layouts.admin')

@section('title', 'Kelengkapan Perangkat Ajar')
@section('page-title', 'Kelengkapan Perangkat Ajar')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bx bx-calendar me-2 fs-4"></i>
                <div>
                    <strong>Tahun Ajaran Aktif:</strong> {{ $activeYear->name }}
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @foreach($types as $type)
            <div class="col-lg-6 col-md-12">
                <div class="card h-100 shadow-sm material-card">
                    <div
                        class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h5 class="mb-0 text-white">
                                <i class="bx bx-folder-open me-2"></i>{{ $type->name }}
                            </h5>
                            @if($type->description)
                                <small class="text-white-50 d-block mt-1">{{ $type->description }}</small>
                            @endif
                        </div>
                        <button class="btn btn-light btn-sm" onclick="openUploadModal({{ $type->id }}, '{{ $type->name }}')">
                            <i class="bx bx-plus"></i> Upload
                        </button>
                    </div>
                    <div class="card-body p-0">

                        @php
                            $myMaterials = $materialGroups->get($type->id);
                        @endphp

                        @if($myMaterials && $myMaterials->isNotEmpty())
                            <div class="list-group list-group-flush">
                                @foreach($myMaterials as $material)
                                    <div class="list-group-item list-group-item-action material-item">
                                        <div class="d-flex w-100 justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold text-dark">{{ $material->description }}</h6>
                                                <div class="d-flex flex-wrap gap-2 mb-2">
                                                    <span class="badge bg-label-primary">
                                                        <i class="bx bx-layer me-1"></i>Kelas {{ $material->grade_level }}
                                                    </span>
                                                    @if($material->subject)
                                                        <span class="badge bg-label-info">
                                                            <i class="bx bx-book me-1"></i>{{ $material->subject->name }}
                                                        </span>
                                                    @endif
                                                    @if($material->status == 'pending')
                                                        <span class="badge bg-warning">
                                                            <i class="bx bx-time-five me-1"></i>Pending
                                                        </span>
                                                    @elseif($material->status == 'approved')
                                                        <span class="badge bg-success">
                                                            <i class="bx bx-check-circle me-1"></i>Disetujui
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger" data-bs-toggle="tooltip"
                                                            title="{{ $material->rejection_note }}">
                                                            <i class="bx bx-x-circle me-1"></i>Ditolak
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            @if($material->file_type == 'file')
                                                <button onclick="previewPdf('{{ asset('storage/' . $material->file_path) }}')"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class="bx bx-book-open me-1"></i>Buka Buku
                                                </button>
                                            @else
                                                <button onclick="previewDrive('{{ $material->link_url }}')"
                                                    class="btn btn-sm btn-outline-warning">
                                                    <i class="bx bxl-google-cloud me-1"></i>Preview Drive
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 text-center">
                                <div class="mb-3">
                                    <i class="bx bx-folder-open text-muted" style="font-size: 3rem;"></i>
                                </div>
                                <p class="text-muted mb-0">Belum ada perangkat yang diupload</p>
                                <small class="text-muted">Klik tombol "Upload" untuk menambahkan</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Upload -->
    <div class="modal fade" id="modalUpload" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formUpload" action="{{ route('transactions.teaching-materials.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload <span id="modalTypeName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Error Container -->
                        <div id="errorContainer" class="alert alert-danger d-none">
                            <ul id="errorList" class="mb-0 small"></ul>
                        </div>

                        <input type="hidden" name="teaching_material_type_id" id="typeId">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tingkat Kelas (Grade)</label>
                                <select name="grade_level" class="form-select" required>
                                    <option value="7">Kelas 7</option>
                                    <option value="8">Kelas 8</option>
                                    <option value="9">Kelas 9</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mata Pelajaran (Opsional)</label>
                                <select name="subject_id" class="form-select">
                                    <option value="">- Pilih Mapel -</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi / Judul</label>
                            <input type="text" name="description" class="form-control"
                                placeholder="Contoh: Modul Ajar Bab 1" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tipe Upload</label>
                            <div class="form-check">
                                <input name="file_type" class="form-check-input" type="radio" value="file" id="typeFile"
                                    checked onchange="toggleType()">
                                <label class="form-check-label" for="typeFile"> Upload File (PDF Max 2MB) </label>
                            </div>
                            <div class="form-check">
                                <input name="file_type" class="form-check-input" type="radio" value="link" id="typeLink"
                                    onchange="toggleType()">
                                <label class="form-check-label" for="typeLink"> Link Google Drive </label>
                            </div>
                        </div>

                        <div id="inputSectionFile">
                            <div class="mb-3">
                                <label class="form-label">File PDF</label>
                                <input type="file" name="file_path" id="inputFilePath" class="form-control"
                                    accept="application/pdf">
                                <div class="form-text">Maksimal ukuran file 2MB. Harus format PDF.</div>
                                <div id="fileFeedback" class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div id="inputSectionLink" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Link Google Drive</label>
                                <input type="url" name="link_url" id="inputLinkUrl" class="form-control"
                                    placeholder="https://drive.google.com/...">
                                <div class="form-text">Pastikan link dapat diakses publik (Anyone with the link).</div>
                                <div id="linkFeedback" class="invalid-feedback"></div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSubmit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
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
                <div class="modal-header border-0 bg-primary text-white">
                    <h5 class="modal-title text-white">
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
        /* Card Hover Effects */
        .material-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
        }

        .material-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        /* Gradient Header */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #696cff 0%, #5a5fc7 100%);
        }

        /* Material Item Styling */
        .material-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            padding: 1rem !important;
        }

        .material-item:hover {
            background-color: #f8f9fa;
            border-left-color: #696cff;
            transform: translateX(5px);
        }

        /* Badge Improvements */
        .badge {
            padding: 0.35em 0.65em;
            font-weight: 500;
        }

        /* Button Hover Effects */
        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Empty State */
        .empty-state-icon {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        /* Alert Styling */
        .alert-info {
            border-left: 4px solid #0dcaf0;
            background-color: #e7f6fd;
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Modal Enhancements */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
            opacity: 1;
        }

        /* Loading Spinner Animation */
        .spinner-border {
            animation: spinner-border 0.75s linear infinite, pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* Modal Backdrop Blur */
        .modal-backdrop.show {
            backdrop-filter: blur(5px);
            background-color: rgba(0, 0, 0, 0.7);
        }

        /* Iframe Smooth Transition */
        #driveFrame {
            transition: opacity 0.5s ease-in-out;
        }

        /* Fullscreen Modal Adjustments */
        .modal-fullscreen .modal-content {
            border-radius: 0;
        }

        /* Loading State Fade */
        #pdfLoader,
        #driveLoader {
            z-index: 10;
            transition: opacity 0.3s ease;
        }
    </style>
@endpush

@push('scripts')
    <!-- DearFlip JS -->
    <script src="https://cdn.jsdelivr.net/npm/dflip/js/libs/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dflip/js/dflip.min.js"></script>

    <script>
        function openUploadModal(typeId, typeName) {
            document.getElementById('typeId').value = typeId;
            document.getElementById('modalTypeName').innerText = typeName;

            // Reset Form & Errors
            document.getElementById('formUpload').reset();
            document.getElementById('errorContainer').classList.add('d-none');
            document.getElementById('inputFilePath').classList.remove('is-invalid', 'is-valid');
            document.getElementById('inputLinkUrl').classList.remove('is-invalid', 'is-valid');
            document.getElementById('fileFeedback').innerText = '';
            document.getElementById('linkFeedback').innerText = '';
            document.getElementById('btnSubmit').disabled = false;
            toggleType();

            new bootstrap.Modal(document.getElementById('modalUpload')).show();
        }

        function toggleType() {
            const isFile = document.getElementById('typeFile').checked;
            const sectionFile = document.getElementById('inputSectionFile');
            const sectionLink = document.getElementById('inputSectionLink');

            if (isFile) {
                sectionFile.style.display = 'block';
                sectionLink.style.display = 'none';
                document.getElementById('inputFilePath').required = true;
                document.getElementById('inputLinkUrl').required = false;
                document.getElementById('inputLinkUrl').classList.remove('is-invalid', 'is-valid');
                document.getElementById('linkFeedback').innerText = '';
            } else {
                sectionFile.style.display = 'none';
                sectionLink.style.display = 'block';
                document.getElementById('inputFilePath').required = false;
                document.getElementById('inputLinkUrl').required = true;
                document.getElementById('inputFilePath').classList.remove('is-invalid', 'is-valid');
                document.getElementById('fileFeedback').innerText = '';
            }
            // Re-evaluate submit button state after toggling
            validateForm();
        }

        function validateForm() {
            const btnSubmit = document.getElementById('btnSubmit');
            const isFileSelected = document.getElementById('typeFile').checked;
            let isValid = true;

            if (isFileSelected) {
                const filePathInput = document.getElementById('inputFilePath');
                if (filePathInput.required && !filePathInput.files[0]) {
                    isValid = false;
                }
                if (filePathInput.classList.contains('is-invalid')) {
                    isValid = false;
                }
            } else {
                const linkUrlInput = document.getElementById('inputLinkUrl');
                if (linkUrlInput.required && !linkUrlInput.value.trim()) {
                    isValid = false;
                }
                if (linkUrlInput.classList.contains('is-invalid')) {
                    isValid = false;
                }
            }

            // Also check other required fields if necessary, e.g., description, grade_level
            const descriptionInput = document.querySelector('input[name="description"]');
            if (!descriptionInput.value.trim()) {
                isValid = false;
            }
            const gradeLevelSelect = document.querySelector('select[name="grade_level"]');
            if (!gradeLevelSelect.value) {
                isValid = false;
            }

            btnSubmit.disabled = !isValid;
        }

        // Frontend Validation: File
        document.getElementById('inputFilePath').addEventListener('change', function () {
            const file = this.files[0];
            const feedback = document.getElementById('fileFeedback');

            this.classList.remove('is-invalid', 'is-valid');
            feedback.innerText = '';

            if (file) {
                if (file.type !== 'application/pdf') {
                    this.classList.add('is-invalid');
                    feedback.innerText = 'File harus berformat PDF.';
                } else if (file.size > 2 * 1024 * 1024) { // 2MB
                    this.classList.add('is-invalid');
                    feedback.innerText = 'Ukuran file melebihi 2MB.';
                } else {
                    this.classList.add('is-valid');
                }
            } else if (this.required) {
                this.classList.add('is-invalid');
                feedback.innerText = 'File PDF wajib diunggah.';
            }
            validateForm();
        });

        // Frontend Validation: Link
        document.getElementById('inputLinkUrl').addEventListener('input', function () {
            const val = this.value;
            const feedback = document.getElementById('linkFeedback');
            const googleDriveRegex = /drive\.google\.com/;

            this.classList.remove('is-invalid', 'is-valid');
            feedback.innerText = '';

            if (val) {
                if (!googleDriveRegex.test(val)) {
                    this.classList.add('is-invalid');
                    feedback.innerText = 'Link harus mengandung drive.google.com';
                } else {
                    this.classList.add('is-valid');
                }
            } else if (this.required) {
                this.classList.add('is-invalid');
                feedback.innerText = 'Link Google Drive wajib diisi.';
            }
            validateForm();
        });

        // Initial validation check for other required fields
        document.querySelector('input[name="description"]').addEventListener('input', validateForm);
        document.querySelector('select[name="grade_level"]').addEventListener('change', validateForm);


        // AJAX Submission
        document.getElementById('formUpload').addEventListener('submit', function (e) {
            e.preventDefault();

            const btn = document.getElementById('btnSubmit');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Menyimpan...';
            btn.disabled = true;

            const formData = new FormData(this);
            const errorContainer = document.getElementById('errorContainer');
            const errorList = document.getElementById('errorList');

            // Reset Errors
            errorContainer.classList.add('d-none');
            errorList.innerHTML = '';

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest', // Important for Laravel to detect AJAX
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                .then(({ status, body }) => {
                    if (status === 200 || status === 201) {
                        $('#modalUpload').modal('hide');
                        // SweetAlert Toast
                        if (typeof Toast !== 'undefined') {
                            Toast.fire({ icon: 'success', title: body.message });
                        } else {
                            alert(body.message);
                        }
                        setTimeout(() => location.reload(), 1500);
                    } else if (status === 422) {
                        // Validation Errors
                        errorContainer.classList.remove('d-none');
                        const errors = body.errors || {};
                        for (const key in errors) {
                            const li = document.createElement('li');
                            li.innerText = errors[key][0];
                            errorList.appendChild(li);
                        }
                    } else {
                        if (typeof Toast !== 'undefined') {
                            Toast.fire({ icon: 'error', title: body.message || 'Terjadi kesalahan' });
                        } else {
                            alert('Error: ' + body.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof Toast !== 'undefined') {
                        Toast.fire({ icon: 'error', title: 'Terjadi kesalahan jaringan' });
                    } else {
                        alert('Terjadi kesalahan jaringan.');
                    }
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    validateForm(); // Re-validate to ensure button state is correct
                });
        });

        // Preview Functions with Loading States
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
                setTimeout(() => {
                    try {
                        loader.style.display = 'none';
                        iframe.style.opacity = '1';
                    } catch (e) {
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

        // Initial call to set the correct state when the page loads
        document.addEventListener('DOMContentLoaded', () => {
            toggleType(); // Set initial required attributes and display
            validateForm(); // Validate form initially
        });
    </script>
@endpush