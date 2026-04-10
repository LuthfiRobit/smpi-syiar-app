@extends('layouts.admin')

@section('title', 'Kelengkapan Perangkat Ajar')
@section('page-title', 'Kelengkapan Perangkat Ajar')

@section('content')

    {{-- ============================================================ --}}
    {{-- HEADER: Active Year + Grade Filter Tabs                       --}}
    {{-- ============================================================ --}}
    <div class="card border-0 shadow-sm mb-4 header-filter-card">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                
                <!-- Left: Active Year Info -->
                <div class="w-100 w-md-auto d-flex justify-content-center justify-content-md-start">
                    <div class="active-year-badge d-flex align-items-center">
                        <div class="badge-icon bg-primary text-white rounded-start d-flex align-items-center justify-content-center">
                            <i class="bx bx-calendar fs-4"></i>
                        </div>
                        <div class="badge-content bg-label-primary px-3 py-2 rounded-end">
                            <span class="text-muted small fw-semibold d-block text-uppercase" style="letter-spacing: 1px; font-size: 0.65rem;">Tahun Ajaran Aktif</span>
                            <span class="h6 mb-0 fw-bold">{{ $activeYear->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Filters -->
                <div class="w-100 w-md-auto d-flex flex-column flex-lg-row align-items-center gap-3">
                    <div class="filter-group d-flex flex-column flex-sm-row align-items-center gap-2 w-100 w-md-auto">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-filter-alt text-muted d-none d-sm-block"></i>
                            <span class="text-dark small fw-bold text-nowrap">Filter Kelas:</span>
                        </div>
                        <div class="btn-group pill-group w-100 w-sm-auto" role="group" id="gradeFilterGroup">
                            <button type="button" class="btn btn-sm btn-primary px-3 active" data-grade="all">Semua</button>
                            <button type="button" class="btn btn-sm btn-outline-primary px-3" data-grade="7">Kelas 7</button>
                            <button type="button" class="btn btn-sm btn-outline-primary px-3" data-grade="8">Kelas 8</button>
                            <button type="button" class="btn btn-sm btn-outline-primary px-3" data-grade="9">Kelas 9</button>
                        </div>
                    </div>
                    
                    <div class="filter-group w-100 w-lg-auto">
                        <select id="subjectFilter" class="form-select form-select-sm border-0 bg-light fw-semibold w-100" style="min-width:180px; border-radius: 8px; height: 38px;">
                            <option value="">Semua Mata Pelajaran</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MATERIAL CARDS (grouped by type)                              --}}
    {{-- ============================================================ --}}
    <div class="row g-4" id="materialCardsContainer">
        @foreach($types as $type)
            @php $myMaterials = $materialGroups->get($type->id); @endphp
            <div class="col-lg-6 col-md-12 type-card-wrapper" data-type-id="{{ $type->id }}">
                <div class="card h-100 shadow-sm material-card">
                    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-3">
                        <div>
                            <h5 class="mb-0 text-white">
                                <i class="bx bx-folder-open me-2"></i>{{ $type->name }}
                            </h5>
                            @if($type->description)
                                <small class="text-white-50 d-block mt-1">{{ $type->description }}</small>
                            @endif
                        </div>
                        <button class="btn btn-light btn-sm"
                            onclick="openUploadModal({{ $type->id }}, '{{ addslashes($type->name) }}')">
                            <i class="bx bx-plus"></i> Upload
                        </button>
                    </div>

                    <div class="card-body p-0">
                        @if($myMaterials && $myMaterials->isNotEmpty())
                            <div class="list-group list-group-flush" id="list-type-{{ $type->id }}">
                                @foreach($myMaterials as $material)
                                    <div class="list-group-item list-group-item-action material-item"
                                        data-grade="{{ $material->grade_level }}"
                                        data-subject-id="{{ $material->subject_id }}"
                                        data-material-id="{{ $material->id }}">

                                        <div class="d-flex w-100 justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold text-dark">{{ $material->description }}</h6>
                                                <div class="d-flex flex-wrap gap-2 mb-1">
                                                    <span class="badge bg-label-primary">
                                                        <i class="bx bx-layer me-1"></i>Kelas {{ $material->grade_level }}
                                                    </span>
                                                    @if($material->subject)
                                                        <span class="badge bg-label-info">
                                                            <i class="bx bx-book me-1"></i>{{ $material->subject->name }}
                                                        </span>
                                                    @endif

                                                    {{-- Status Badge --}}
                                                    @if($material->status == 'pending')
                                                        <span class="badge bg-warning">
                                                            <i class="bx bx-time-five me-1"></i>Menunggu Review
                                                        </span>
                                                    @elseif($material->status == 'approved')
                                                        <span class="badge bg-success">
                                                            <i class="bx bx-check-circle me-1"></i>Disetujui
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="bx bx-x-circle me-1"></i>Ditolak
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- Rejection Note — visible to teacher --}}
                                                @if($material->status == 'rejected' && $material->rejection_note)
                                                    <div class="alert alert-danger py-1 px-2 mb-1 small d-flex align-items-start gap-2">
                                                        <i class="bx bx-info-circle mt-1 flex-shrink-0"></i>
                                                        <span><strong>Catatan Admin:</strong> {{ $material->rejection_note }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="d-flex gap-2 flex-wrap">
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

                                            {{-- Revise button — only for rejected --}}
                                            @if($material->status == 'rejected')
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="openReviseModal(
                                                        {{ $material->id }},
                                                        '{{ $material->file_type }}',
                                                        '{{ addslashes($material->description) }}',
                                                        '{{ $material->grade_level }}',
                                                        {{ $material->subject_id ?? 'null' }},
                                                        '{{ $material->link_url }}'
                                                    )">
                                                    <i class="bx bx-edit me-1"></i>Revisi
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 text-center empty-state">
                                <i class="bx bx-folder-open text-muted empty-state-icon" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-0 mt-2">Belum ada perangkat yang diupload</p>
                                <small class="text-muted">Klik tombol "Upload" untuk menambahkan</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: Upload New Material                                     --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalUpload" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formUpload" action="{{ route('transactions.teaching-materials.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload <span id="modalTypeName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="uploadErrorContainer" class="alert alert-danger d-none">
                            <ul id="uploadErrorList" class="mb-0 small"></ul>
                        </div>
                        <input type="hidden" name="teaching_material_type_id" id="typeId">
                        @include('transactions.teaching_material._form_fields', ['prefix' => 'upload'])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSubmitUpload" class="btn btn-primary">
                            <i class="bx bx-upload me-1"></i>Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: Revise Rejected Material                               --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalRevise" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formRevise" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-content border-danger">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title text-white"><i class="bx bx-edit me-2"></i>Revisi Perangkat Ajar</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning small mb-3">
                            <i class="bx bx-info-circle me-1"></i>
                            Setelah direvisi, modul akan kembali ke status <strong>Pending</strong> untuk direview ulang oleh admin.
                        </div>
                        <div id="reviseErrorContainer" class="alert alert-danger d-none">
                            <ul id="reviseErrorList" class="mb-0 small"></ul>
                        </div>
                        @include('transactions.teaching_material._form_fields', ['prefix' => 'revise'])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btnSubmitRevise" class="btn btn-danger">
                            <i class="bx bx-send me-1"></i>Kirim Revisi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: Preview PDF (Flipbook)                                 --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalPreviewPdf" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0 bg-dark text-white">
                    <h5 class="modal-title"><i class="bx bx-book-open me-2"></i>Preview Buku PDF</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 position-relative">
                    <div id="pdfLoader" class="position-absolute top-50 start-50 translate-middle text-center">
                        <div class="spinner-border text-light" role="status" style="width:3rem;height:3rem;"></div>
                        <p class="text-light mt-3">Memuat buku...</p>
                    </div>
                    <div id="flipbookContainer" class="w-100 h-100" style="min-height: 80vh;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: Preview Drive                                          --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalPreviewDrive" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header border-0 bg-primary text-white">
                    <h5 class="modal-title text-white">
                        <i class="bx bxl-google-cloud me-2"></i>Preview Google Drive
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 position-relative bg-light">
                    <div id="driveLoader" class="position-absolute top-50 start-50 translate-middle text-center">
                        <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div>
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
    <link href="https://cdn.jsdelivr.net/npm/dflip/css/dflip.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/dflip/css/themify-icons.min.css" rel="stylesheet">
    <style>
        .material-card { transition: transform .3s ease, box-shadow .3s ease; border: none; }
        .material-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,.15) !important; }
        .bg-gradient-primary { background: linear-gradient(135deg, #696cff 0%, #5a5fc7 100%); }
        .material-item { transition: all .2s ease; border-left: 3px solid transparent; padding: 1rem !important; }
        .material-item:hover { background-color: #f8f9fa; border-left-color: #696cff; }
        .material-item.d-none-filtered { display: none !important; }
        .empty-state-icon { animation: float 3s ease-in-out infinite; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .alert-info { border-left: 4px solid #0dcaf0; background-color: #e7f6fd; }
        .modal-backdrop.show { backdrop-filter: blur(5px); background-color: rgba(0,0,0,.7); }
        #driveFrame { transition: opacity .5s ease-in-out; }
        
        /* Header Filter Styles */
        .header-filter-card { border-radius: 12px; }
        .active-year-badge .badge-icon { width: 45px; height: 52px; }
        .active-year-badge .badge-content { min-width: 140px; }
        .pill-group { background: #f0f1f4; padding: 4px; border-radius: 30px; }
        .pill-group .btn { border-radius: 25px !important; border: none !important; font-weight: 600; font-size: 0.75rem; transition: all 0.2s ease; flex: 1; }
        .pill-group .btn.active { box-shadow: 0 4px 8px rgba(105, 108, 255, 0.3); }
        .pill-group .btn-outline-primary { color: #566a7f; }
        .pill-group .btn-outline-primary:hover { background: rgba(105, 108, 255, 0.1); color: #696cff; }
        #subjectFilter:focus { box-shadow: none; border-color: transparent; background-color: #eef0f2; }
        
        @media (max-width: 576px) {
            .active-year-badge .badge-content { min-width: 0; flex-grow: 1; }
            .pill-group .btn { padding-left: 10px !important; padding-right: 10px !important; font-size: 0.7rem; }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/dflip/js/libs/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dflip/js/dflip.min.js"></script>
    <script>
    // ─────────────────────────────────────────────────────
    // Grade & Subject Filter
    // ─────────────────────────────────────────────────────
    let activeGrade   = 'all';
    let activeSubject = '';

    function applyFilters() {
        document.querySelectorAll('.material-item').forEach(function (item) {
            const grade   = item.dataset.grade;
            const subjId  = item.dataset.subjectId;
            const gradeOk = activeGrade   === 'all' || grade  === activeGrade;
            const subjOk  = activeSubject === ''    || subjId === activeSubject;
            item.style.display = (gradeOk && subjOk) ? '' : 'none';
        });
    }

    document.querySelectorAll('#gradeFilterGroup .btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('#gradeFilterGroup .btn').forEach(b => {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-outline-primary');
            });
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-outline-primary');
            activeGrade = this.dataset.grade;
            applyFilters();
        });
    });

    document.getElementById('subjectFilter').addEventListener('change', function () {
        activeSubject = this.value;
        applyFilters();
    });

    // ─────────────────────────────────────────────────────
    // Upload Modal
    // ─────────────────────────────────────────────────────
    function openUploadModal(typeId, typeName) {
        document.getElementById('typeId').value        = typeId;
        document.getElementById('modalTypeName').innerText = typeName;
        document.getElementById('formUpload').reset();
        document.getElementById('uploadErrorContainer').classList.add('d-none');
        document.getElementById('uploadErrorList').innerHTML = '';
        toggleUploadType();
        new bootstrap.Modal(document.getElementById('modalUpload')).show();
    }

    function toggleUploadType() {
        const isFile = document.getElementById('upload-typeFile').checked;
        document.getElementById('upload-inputSectionFile').style.display = isFile ? 'block' : 'none';
        document.getElementById('upload-inputSectionLink').style.display = isFile ? 'none'  : 'block';
        document.getElementById('upload-inputFilePath').required = isFile;
        document.getElementById('upload-inputLinkUrl').required  = !isFile;
    }

    document.getElementById('formUpload').addEventListener('submit', function (e) {
        e.preventDefault();
        submitForm(this, 'btnSubmitUpload', 'uploadErrorContainer', 'uploadErrorList', function () {
            location.reload();
        });
    });

    // ─────────────────────────────────────────────────────
    // Revise Modal
    // ─────────────────────────────────────────────────────
    function openReviseModal(id, fileType, description, gradeLevel, subjectId, linkUrl) {
        const form = document.getElementById('formRevise');
        form.action = `/transactions/teaching-materials/${id}`;
        form.reset();

        // Pre-fill fields
        document.querySelector('#formRevise input[name="description"]').value = description;
        document.querySelector('#formRevise select[name="grade_level"]').value = gradeLevel;

        const subjSelect = document.querySelector('#formRevise select[name="subject_id"]');
        if (subjSelect && subjectId) subjSelect.value = subjectId;

        if (fileType === 'link') {
            document.getElementById('revise-typeLink').checked = true;
            const linkInput = document.getElementById('revise-inputLinkUrl');
            if (linkInput) linkInput.value = linkUrl || '';
        } else {
            document.getElementById('revise-typeFile').checked = true;
        }
        toggleReviseType();

        document.getElementById('reviseErrorContainer').classList.add('d-none');
        document.getElementById('reviseErrorList').innerHTML = '';
        new bootstrap.Modal(document.getElementById('modalRevise')).show();
    }

    function toggleReviseType() {
        const isFile = document.getElementById('revise-typeFile').checked;
        document.getElementById('revise-inputSectionFile').style.display = isFile ? 'block' : 'none';
        document.getElementById('revise-inputSectionLink').style.display = isFile ? 'none'  : 'block';
        document.getElementById('revise-inputFilePath').required = isFile;
        document.getElementById('revise-inputLinkUrl').required  = !isFile;
    }

    document.getElementById('formRevise').addEventListener('submit', function (e) {
        e.preventDefault();
        submitForm(this, 'btnSubmitRevise', 'reviseErrorContainer', 'reviseErrorList', function () {
            location.reload();
        });
    });

    // ─────────────────────────────────────────────────────
    // Generic AJAX form submitter
    // ─────────────────────────────────────────────────────
    function submitForm(form, btnId, errorContainerId, errorListId, onSuccess) {
        const btn = document.getElementById(btnId);
        const origHtml = btn.innerHTML;
        btn.innerHTML  = '<i class="bx bx-loader-alt bx-spin"></i> Menyimpan...';
        btn.disabled   = true;

        document.getElementById(errorContainerId).classList.add('d-none');
        document.getElementById(errorListId).innerHTML = '';

        fetch(form.action, {
            method : 'POST',
            body   : new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json().then(data => ({ status: r.status, body: data })))
        .then(({ status, body }) => {
            if (status === 200 || status === 201) {
                const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                if (modal) modal.hide();
                if (typeof Toast !== 'undefined') Toast.fire({ icon: 'success', title: body.message });
                setTimeout(onSuccess, 1000);
            } else if (status === 422) {
                document.getElementById(errorContainerId).classList.remove('d-none');
                const errors = body.errors || {};
                for (const key in errors) {
                    const li = document.createElement('li');
                    li.innerText = errors[key][0];
                    document.getElementById(errorListId).appendChild(li);
                }
            } else {
                if (typeof Toast !== 'undefined') Toast.fire({ icon: 'error', title: body.message || 'Terjadi kesalahan.' });
            }
        })
        .catch(() => {
            if (typeof Toast !== 'undefined') Toast.fire({ icon: 'error', title: 'Terjadi kesalahan jaringan.' });
        })
        .finally(() => {
            btn.innerHTML = origHtml;
            btn.disabled  = false;
        });
    }

    // ─────────────────────────────────────────────────────
    // Preview PDF
    // ─────────────────────────────────────────────────────
    function previewPdf(url) {
        $('#pdfLoader').show();
        $('#flipbookContainer').html('').hide();
        new bootstrap.Modal(document.getElementById('modalPreviewPdf')).show();
        setTimeout(function () {
            try {
                $('#flipbookContainer').flipBook(url, {
                    source: url, backgroundColor: '#222', height: '100%', duration: 700,
                    onReady: function () {
                        $('#pdfLoader').fadeOut(300, function () { $('#flipbookContainer').fadeIn(500); });
                    }
                });
            } catch (e) {
                $('#pdfLoader').html('<div class="text-light"><i class="bx bx-error-circle fs-1"></i><p class="mt-3">Gagal memuat PDF</p></div>');
            }
        }, 300);
    }

    // ─────────────────────────────────────────────────────
    // Preview Drive
    // ─────────────────────────────────────────────────────
    function previewDrive(url) {
        const iframe = document.getElementById('driveFrame');
        const loader = document.getElementById('driveLoader');
        loader.innerHTML = '<div class="spinner-border text-primary" style="width:3rem;height:3rem;"></div><p class="text-muted mt-3">Memuat dokumen dari Google Drive...</p>';
        loader.style.display = 'block';
        iframe.style.opacity = '0';

        let previewUrl = url.replace(/\/view.*$/, '/preview').replace(/\/edit.*$/, '/preview');
        if (!previewUrl.includes('preview') && url.includes('drive.google.com')) {
            previewUrl = url.endsWith('/') ? url + 'preview' : url + '/preview';
        }

        new bootstrap.Modal(document.getElementById('modalPreviewDrive')).show();
        iframe.src = previewUrl;
        iframe.style.display = 'block';
        iframe.onload = function () {
            setTimeout(function () { loader.style.display = 'none'; iframe.style.opacity = '1'; }, 500);
        };
        setTimeout(function () {
            if (loader.style.display !== 'none') {
                loader.innerHTML = `<div class="text-warning text-center"><i class="bx bx-time-five" style="font-size:3rem;"></i><p class="mt-3 fw-bold">Link Drive Belum Public</p><a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">Buka di Tab Baru</a></div>`;
            }
        }, 8000);
    }

    // ─────────────────────────────────────────────────────
    // Reset modals on close
    // ─────────────────────────────────────────────────────
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
    </script>
@endpush