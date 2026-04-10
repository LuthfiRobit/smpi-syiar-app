@extends('layouts.admin')

@section('title', 'Monitoring Kelengkapan Perangkat Ajar')
@section('page-title', 'Monitoring Perangkat Ajar')

@section('content')

    {{-- ============================================================ --}}
    {{-- FILTER BAR                                                    --}}
    {{-- ============================================================ --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0 fw-semibold text-muted small">Tahun Ajaran</label>
                    <select class="form-select form-select-sm" id="filterYear" style="min-width:180px;">
                        @foreach($years as $year)
                            <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>
                                {{ $year->name }} {{ $year->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0 fw-semibold text-muted small">Guru</label>
                    <select class="form-select form-select-sm" id="filterTeacher" style="min-width:180px;">
                        <option value="">Semua Guru</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0 fw-semibold text-muted small">Kelas</label>
                    <select class="form-select form-select-sm" id="filterGrade" style="min-width:130px;">
                        <option value="">Semua Kelas</option>
                        <option value="7">Kelas 7</option>
                        <option value="8">Kelas 8</option>
                        <option value="9">Kelas 9</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0 fw-semibold text-muted small">Mata Pelajaran</label>
                    <select class="form-select form-select-sm" id="filterSubject" style="min-width:180px;">
                        <option value="">Semua Mapel</option>
                        @foreach($subjects as $subj)
                            <option value="{{ $subj->id }}">{{ $subj->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label mb-0 fw-semibold text-muted small">Status</label>
                    <select class="form-select form-select-sm" id="filterStatus" style="min-width:140px;">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
                <div class="col-auto ms-auto d-flex align-items-end">
                    <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()">
                        <i class="bx bx-reset me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- SUMMARY CARDS                                                 --}}
    {{-- ============================================================ --}}
    <div class="row g-3 mb-4" id="summaryCards">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="avatar avatar-md bg-label-info rounded">
                        <i class="bx bx-file fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5" id="sumTotal">—</div>
                        <small class="text-muted">Total Modul</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="avatar avatar-md bg-label-success rounded">
                        <i class="bx bx-check-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5" id="sumApproved">—</div>
                        <small class="text-muted">Disetujui</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="avatar avatar-md bg-label-warning rounded">
                        <i class="bx bx-time-five fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5" id="sumPending">—</div>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <div class="avatar avatar-md bg-label-danger rounded">
                        <i class="bx bx-x-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5" id="sumRejected">—</div>
                        <small class="text-muted">Ditolak</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TAB NAVIGATION                                                --}}
    {{-- ============================================================ --}}
    <div class="card">
        <div class="card-header bg-white border-bottom">
            <ul class="nav nav-tabs card-header-tabs" id="mainTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-semibold" id="tab-guru-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-guru" type="button" role="tab">
                        <i class="bx bx-user me-1"></i>Per Guru
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" id="tab-modul-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-modul" type="button" role="tab">
                        <i class="bx bx-file me-1"></i>Per Modul
                        <span class="badge bg-primary ms-1" id="modulCount" style="display:none;"></span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content">

            {{-- =============================== TAB 1: PER GURU --}}
            <div class="tab-pane fade show active" id="tab-guru" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="ps-3">No</th>
                                <th>Nama Guru</th>
                                <th>NIP</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Disetujui</th>
                                <th class="text-center">Pending</th>
                                <th class="text-center">Ditolak</th>
                                <th class="text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tablePerGuru">
                            <tr><td colspan="8" class="text-center py-4 text-muted">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- =============================== TAB 2: PER MODUL --}}
            <div class="tab-pane fade" id="tab-modul" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="4%" class="ps-3">No</th>
                                <th>Guru</th>
                                <th>Kelas</th>
                                <th>Mapel</th>
                                <th>Tipe</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" width="12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tablePerModul">
                            <tr><td colspan="8" class="text-center py-4 text-muted">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>{{-- end tab-content --}}
    </div>


    {{-- ============================================================ --}}
    {{-- MODAL: Detail Per Guru                                        --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalDetailGuru" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background:linear-gradient(135deg,#696cff,#5a5fc7);">
                    <h5 class="modal-title text-white">
                        <i class="bx bx-detail me-2"></i>Detail Modul — <span id="guruDetailName"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="guruDetailContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">Memuat detail...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: Reject Module                                          --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bx bx-x-circle me-2"></i>Tolak Modul</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="rejectModuleId">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan Penolakan <span class="text-danger">*</span></label>
                        <textarea id="rejectionNote" class="form-control" rows="4"
                            placeholder="Jelaskan alasan penolakan kepada guru..."></textarea>
                        <div class="invalid-feedback" id="rejectNoteError"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmReject">
                        <i class="bx bx-x-circle me-1"></i>Tolak Modul
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: Preview PDF (Flipbook)                                 --}}
    {{-- ============================================================ --}}
    <div class="modal fade" id="modalPreviewPdf" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0 bg-dark text-white">
                    <h5 class="modal-title"><i class="bx bx-book-open me-2"></i>Preview PDF</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 position-relative">
                    <div id="pdfLoader" class="position-absolute top-50 start-50 translate-middle text-center">
                        <div class="spinner-border text-light" role="status" style="width:3rem;height:3rem;"></div>
                        <p class="text-light mt-3">Memuat buku...</p>
                    </div>
                    <div id="flipbookContainer" class="w-100 h-100" style="min-height:80vh;"></div>
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
                    <h5 class="modal-title text-white"><i class="bx bxl-google-cloud me-2"></i>Preview Google Drive</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 position-relative bg-light">
                    <div id="driveLoader" class="position-absolute top-50 start-50 translate-middle text-center">
                        <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div>
                        <p class="text-muted mt-3">Memuat dokumen...</p>
                    </div>
                    <iframe id="driveFrame" src="" class="w-100 h-100"
                        style="min-height:90vh;border:none;opacity:0;transition:opacity .3s ease;"></iframe>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/dflip/css/dflip.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/dflip/css/themify-icons.min.css" rel="stylesheet">
    <style>
        .status-pill { display: inline-flex; align-items: center; gap: .3rem; padding: .3em .7em; border-radius: 20px; font-size: .8rem; font-weight: 600; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-pending  { background: #fef9c3; color: #854d0e; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-none     { background: #f3f4f6; color: #6b7280; }
        .avatar { display:flex; align-items:center; justify-content:center; width:40px; height:40px; }
        .avatar-md { width:48px; height:48px; }
        .action-btns .btn { padding: .25rem .55rem; font-size:.8rem; }
        .table th { white-space: nowrap; font-size: .83rem; }
        .table td { font-size: .875rem; vertical-align: middle; }
        .nav-tabs .nav-link { color: #6b7280; }
        .nav-tabs .nav-link.active { color: #696cff; border-bottom-color: #696cff; font-weight: 700; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/dflip/js/libs/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dflip/js/dflip.min.js"></script>
    <script>
    $(function () {

        // ─────────────────────────────────────────────
        // Helpers
        // ─────────────────────────────────────────────
        function getFilters() {
            return {
                year_id    : $('#filterYear').val(),
                teacher_id : $('#filterTeacher').val(),
                grade_level: $('#filterGrade').val(),
                subject_id : $('#filterSubject').val(),
                status     : $('#filterStatus').val(),
            };
        }

        function resetFilters() {
            $('#filterTeacher, #filterGrade, #filterSubject, #filterStatus').val('');
            loadAll();
        }
        window.resetFilters = resetFilters;

        function statusBadge(status) {
            const map = {
                approved : `<span class="status-pill status-approved"><i class="bx bx-check-circle"></i> Disetujui</span>`,
                pending  : `<span class="status-pill status-pending"><i class="bx bx-time-five"></i> Pending</span>`,
                rejected : `<span class="status-pill status-rejected"><i class="bx bx-x-circle"></i> Ditolak</span>`,
            };
            return map[status] || `<span class="status-pill status-none">—</span>`;
        }

        // ─────────────────────────────────────────────
        // Load: Per Guru (Tab 1)
        // ─────────────────────────────────────────────
        function loadPerGuru() {
            const f = getFilters();
            $('#tablePerGuru').html('<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');

            $.get("{{ route('transactions.teaching-materials.admin-data') }}", { year_id: f.year_id, teacher_id: f.teacher_id }, function (data) {
                let html = '';
                let totalAll = 0, approvedAll = 0, pendingAll = 0, rejectedAll = 0;

                if (data.teachers.length > 0) {
                    data.teachers.forEach(function (t, i) {
                        totalAll    += t.total;
                        approvedAll += t.approved;
                        pendingAll  += t.pending;
                        rejectedAll += t.rejected;
                        html += `
                        <tr>
                            <td class="ps-3">${i+1}</td>
                            <td><strong>${t.name}</strong></td>
                            <td><span class="badge bg-label-secondary">${t.nip || '—'}</span></td>
                            <td class="text-center"><span class="badge bg-label-info">${t.total}</span></td>
                            <td class="text-center"><span class="badge bg-label-success">${t.approved}</span></td>
                            <td class="text-center"><span class="badge bg-label-warning">${t.pending}</span></td>
                            <td class="text-center"><span class="badge bg-label-danger">${t.rejected}</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary btn-detail-guru"
                                    data-teacher-id="${t.id}" data-teacher-name="${t.name}">
                                    <i class="bx bx-detail"></i> Detail
                                </button>
                            </td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data guru.</td></tr>';
                }

                $('#tablePerGuru').html(html);
                updateSummaryCards(totalAll, approvedAll, pendingAll, rejectedAll);
            }).fail(function () {
                $('#tablePerGuru').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>');
            });
        }

        // ─────────────────────────────────────────────
        // Load: Per Modul (Tab 2)
        // ─────────────────────────────────────────────
        function loadPerModul() {
            const f = getFilters();
            $('#tablePerModul').html('<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>');

            $.get("{{ route('transactions.teaching-materials.admin-modules') }}", f, function (data) {
                let html = '';
                const mods = data.modules;

                $('#modulCount').text(mods.length).show();

                if (mods.length > 0) {
                    mods.forEach(function (m, i) {
                        const fileBtn = m.file_type === 'file'
                            ? `<button onclick="previewPdf('/storage/${m.file_path}')" class="btn btn-sm btn-outline-info"><i class="bx bx-book-open"></i></button>`
                            : `<button onclick="previewDrive('${m.link_url}')" class="btn btn-sm btn-outline-warning"><i class="bx bxl-google-cloud"></i></button>`;

                        const approveBtn = m.status !== 'approved'
                            ? `<button onclick="approveModule(${m.id}, this)" class="btn btn-sm btn-outline-success"><i class="bx bx-check"></i></button>`
                            : '';
                        const rejectBtn = m.status !== 'rejected'
                            ? `<button onclick="openReject(${m.id})" class="btn btn-sm btn-outline-danger"><i class="bx bx-x"></i></button>`
                            : '';

                        html += `
                        <tr id="row-module-${m.id}">
                            <td class="ps-3">${i+1}</td>
                            <td>${m.teacher ? m.teacher.name : '—'}</td>
                            <td><span class="badge bg-label-primary">Kelas ${m.grade_level}</span></td>
                            <td>${m.subject ? `<span class="badge bg-label-info">${m.subject.name}</span>` : '<span class="text-muted small">—</span>'}</td>
                            <td>${m.type ? m.type.name : '—'}</td>
                            <td class="text-wrap" style="max-width:200px;">${m.description || '—'}</td>
                            <td class="text-center" id="status-${m.id}">${statusBadge(m.status)}</td>
                            <td class="text-center action-btns">
                                <div class="d-flex gap-1 justify-content-center">
                                    ${fileBtn}${approveBtn}${rejectBtn}
                                </div>
                            </td>
                        </tr>`;
                    });
                } else {
                    html = '<tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada modul ditemukan.</td></tr>';
                }

                $('#tablePerModul').html(html);
            }).fail(function () {
                $('#tablePerModul').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>');
            });
        }

        function loadAll() {
            loadPerGuru();
            loadPerModul();
        }

        // ─────────────────────────────────────────────
        // Summary Cards
        // ─────────────────────────────────────────────
        function updateSummaryCards(total, approved, pending, rejected) {
            $('#sumTotal').text(total);
            $('#sumApproved').text(approved);
            $('#sumPending').text(pending);
            $('#sumRejected').text(rejected);
        }

        // ─────────────────────────────────────────────
        // Filter events
        // ─────────────────────────────────────────────
        $('#filterYear, #filterTeacher, #filterGrade, #filterSubject, #filterStatus').on('change', loadAll);

        // ─────────────────────────────────────────────
        // Detail Guru Modal
        // ─────────────────────────────────────────────
        $(document).on('click', '.btn-detail-guru', function () {
            const teacherId   = $(this).data('teacher-id');
            const teacherName = $(this).data('teacher-name');
            const yearId      = $('#filterYear').val();

            $('#guruDetailName').text(teacherName);
            $('#guruDetailContent').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3 text-muted">Memuat detail...</p></div>');
            $('#modalDetailGuru').modal('show');

            $.get("{{ route('transactions.teaching-materials.admin-detail') }}", { teacher_id: teacherId, year_id: yearId }, function (data) {
                let html = '<div class="row g-3">';
                data.types.forEach(function (type) {
                    const typeMaterials = data.materials.filter(m => m.teaching_material_type_id == type.id);
                    const hasMaterial   = typeMaterials.length > 0;
                    html += `
                    <div class="col-md-6">
                        <div class="card ${hasMaterial ? 'border-success' : 'border-secondary'} h-100">
                            <div class="card-header d-flex justify-content-between align-items-center py-2">
                                <h6 class="mb-0"><i class="bx bx-folder-open me-1"></i>${type.name}</h6>
                                ${hasMaterial
                                    ? `<span class="badge bg-success">${typeMaterials.length} Modul</span>`
                                    : `<span class="badge bg-secondary">Belum Upload</span>`}
                            </div>
                            <div class="card-body p-2">`;

                    if (typeMaterials.length > 0) {
                        typeMaterials.forEach(function (m) {
                            const fileBtn = m.file_type === 'file'
                                ? `<button onclick="previewPdf('/storage/${m.file_path}')" class="btn btn-xs btn-outline-info py-0 px-1"><i class="bx bx-book-open"></i></button>`
                                : `<button onclick="previewDrive('${m.link_url}')" class="btn btn-xs btn-outline-warning py-0 px-1"><i class="bx bxl-google-cloud"></i></button>`;
                            const approveBtn = m.status !== 'approved'
                                ? `<button onclick="approveModule(${m.id}, this)" class="btn btn-xs btn-outline-success py-0 px-1"><i class="bx bx-check"></i></button>`
                                : '';
                            const rejectBtn = m.status !== 'rejected'
                                ? `<button onclick="openReject(${m.id})" class="btn btn-xs btn-outline-danger py-0 px-1"><i class="bx bx-x"></i></button>`
                                : '';

                            html += `
                            <div class="border rounded p-2 mb-2">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <p class="mb-1 fw-semibold small">${m.description || '—'}</p>
                                        <div class="d-flex flex-wrap gap-1">
                                            <span class="badge bg-label-primary">Kelas ${m.grade_level}</span>
                                            ${m.subject ? `<span class="badge bg-label-info">${m.subject.name}</span>` : ''}
                                        </div>
                                        ${m.status === 'rejected' && m.rejection_note
                                            ? `<p class="text-danger small mt-1 mb-0"><i class="bx bx-info-circle me-1"></i>${m.rejection_note}</p>`
                                            : ''}
                                    </div>
                                    <div id="status-${m.id}">${statusBadge(m.status)}</div>
                                </div>
                                <div class="d-flex gap-1 mt-2">${fileBtn}${approveBtn}${rejectBtn}</div>
                            </div>`;
                        });
                    } else {
                        html += `<p class="text-muted small mb-0">Belum ada upload untuk tipe ini.</p>`;
                    }

                    html += `</div></div></div>`;
                });
                html += '</div>';
                $('#guruDetailContent').html(html);
            }).fail(function () {
                $('#guruDetailContent').html('<div class="alert alert-danger">Gagal memuat detail.</div>');
            });
        });

        // ─────────────────────────────────────────────
        // Approve
        // ─────────────────────────────────────────────
        window.approveModule = function (id, btn) {
            if (!confirm('Setujui modul ini?')) return;
            $(btn).prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i>');

            $.ajax({
                url    : `/transactions/teaching-materials/${id}/approve`,
                method : 'POST',
                data   : { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    $(`#status-${id}`).html(statusBadge('approved'));
                    // Remove approve/reject buttons from this row
                    $(btn).closest('tr, .border.rounded').find('.btn-outline-success, .btn-outline-danger').remove();
                    loadPerGuru(); // refresh summary
                    if (typeof Toast !== 'undefined') Toast.fire({ icon: 'success', title: res.message });
                },
                error: function (xhr) {
                    $(btn).prop('disabled', false).html('<i class="bx bx-check"></i>');
                    if (typeof Toast !== 'undefined') Toast.fire({ icon: 'error', title: 'Gagal menyetujui modul.' });
                }
            });
        };

        // ─────────────────────────────────────────────
        // Reject Modal
        // ─────────────────────────────────────────────
        window.openReject = function (id) {
            $('#rejectModuleId').val(id);
            $('#rejectionNote').val('').removeClass('is-invalid');
            $('#rejectNoteError').text('');
            $('#modalReject').modal('show');
        };

        $('#btnConfirmReject').on('click', function () {
            const id   = $('#rejectModuleId').val();
            const note = $('#rejectionNote').val().trim();
            const btn  = $(this);

            if (!note) {
                $('#rejectionNote').addClass('is-invalid');
                $('#rejectNoteError').text('Catatan penolakan wajib diisi.');
                return;
            }

            btn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i>Menolak...');

            $.ajax({
                url    : `/transactions/teaching-materials/${id}/reject`,
                method : 'POST',
                data   : { _token: '{{ csrf_token() }}', rejection_note: note },
                success: function (res) {
                    $(`#status-${id}`).html(statusBadge('rejected'));
                    $(`#status-${id}`).closest('tr, .border.rounded').find('.btn-outline-success, .btn-outline-danger').remove();
                    $('#modalReject').modal('hide');
                    loadPerGuru();
                    if (typeof Toast !== 'undefined') Toast.fire({ icon: 'warning', title: res.message });
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.errors?.rejection_note?.[0] || 'Gagal menolak modul.';
                    $('#rejectionNote').addClass('is-invalid');
                    $('#rejectNoteError').text(msg);
                }
            }).always(function () {
                btn.prop('disabled', false).html('<i class="bx bx-x-circle me-1"></i>Tolak Modul');
            });
        });

        // ─────────────────────────────────────────────
        // Preview PDF
        // ─────────────────────────────────────────────
        window.previewPdf = function (url) {
            $('#pdfLoader').show();
            $('#flipbookContainer').html('').hide();
            new bootstrap.Modal(document.getElementById('modalPreviewPdf')).show();
            setTimeout(function () {
                try {
                    $('#flipbookContainer').flipBook(url, {
                        source         : url,
                        backgroundColor: '#222',
                        height         : '100%',
                        duration       : 700,
                        onReady: function () {
                            $('#pdfLoader').fadeOut(300, function () { $('#flipbookContainer').fadeIn(500); });
                        }
                    });
                } catch (e) {
                    $('#pdfLoader').html('<div class="text-light"><i class="bx bx-error-circle fs-1"></i><p class="mt-3">Gagal memuat PDF</p></div>');
                }
            }, 300);
        };

        // ─────────────────────────────────────────────
        // Preview Drive
        // ─────────────────────────────────────────────
        window.previewDrive = function (url) {
            const iframe = document.getElementById('driveFrame');
            const loader = document.getElementById('driveLoader');
            loader.innerHTML = `<div class="spinner-border text-primary" style="width:3rem;height:3rem;"></div><p class="text-muted mt-3">Memuat dokumen...</p>`;
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
                    loader.innerHTML = `<div class="text-warning text-center"><i class="bx bx-time-five" style="font-size:3rem;"></i><p class="mt-3 fw-bold">Timeout atau link belum publik</p><a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">Buka di Tab Baru</a></div>`;
                }
            }, 8000);
        };

        // ─────────────────────────────────────────────
        // Reset modals on close
        // ─────────────────────────────────────────────
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

        // ─────────────────────────────────────────────
        // Trigger on tab switch (lazy load Per Modul)
        // ─────────────────────────────────────────────
        $('#tab-modul-btn').on('shown.bs.tab', loadPerModul);

        // ─────────────────────────────────────────────
        // Initial Load
        // ─────────────────────────────────────────────
        loadPerGuru();
        loadPerModul();
    });
    </script>
@endpush