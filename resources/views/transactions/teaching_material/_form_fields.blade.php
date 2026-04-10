{{--
    Shared form fields partial for Teaching Material Upload & Revise modals.
    Usage: @include('transactions.teaching_material._form_fields', ['prefix' => 'upload'])
           @include('transactions.teaching_material._form_fields', ['prefix' => 'revise'])
    The $prefix variable namespaces all IDs to prevent conflicts when both modals exist on the same page.
--}}
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Tingkat Kelas <span class="text-danger">*</span></label>
        <select name="grade_level" class="form-select" required>
            <option value="">- Pilih Kelas -</option>
            <option value="7">Kelas 7</option>
            <option value="8">Kelas 8</option>
            <option value="9">Kelas 9</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Mata Pelajaran <span class="text-muted small">(Opsional)</span></label>
        <select name="subject_id" id="{{ $prefix }}-subjectSelect" class="form-select">
            <option value="">- Pilih Mapel -</option>
            @foreach(\App\Models\Subject::orderBy('name')->get() as $subject)
                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Deskripsi / Judul <span class="text-danger">*</span></label>
    <input type="text" name="description" class="form-control"
        placeholder="Contoh: Modul Ajar Bab 1 - Bilangan Bulat" required maxlength="255">
</div>

<div class="mb-3">
    <label class="form-label">Tipe Upload <span class="text-danger">*</span></label>
    <div class="d-flex gap-3">
        <div class="form-check">
            <input name="file_type" class="form-check-input" type="radio" value="file"
                id="{{ $prefix }}-typeFile" checked onchange="toggle{{ ucfirst($prefix) }}Type()">
            <label class="form-check-label" for="{{ $prefix }}-typeFile">
                <i class="bx bx-file me-1"></i>Upload File PDF
            </label>
        </div>
        <div class="form-check">
            <input name="file_type" class="form-check-input" type="radio" value="link"
                id="{{ $prefix }}-typeLink" onchange="toggle{{ ucfirst($prefix) }}Type()">
            <label class="form-check-label" for="{{ $prefix }}-typeLink">
                <i class="bx bxl-google-cloud me-1"></i>Link Google Drive
            </label>
        </div>
    </div>
</div>

<div id="{{ $prefix }}-inputSectionFile">
    <div class="mb-3">
        <label class="form-label">File PDF <span class="text-danger">*</span></label>
        <input type="file" name="file_path" id="{{ $prefix }}-inputFilePath"
            class="form-control" accept="application/pdf">
        <div class="form-text text-muted">Maksimal 2MB. Format PDF saja.</div>
    </div>
</div>

<div id="{{ $prefix }}-inputSectionLink" style="display:none;">
    <div class="mb-3">
        <label class="form-label">Link Google Drive <span class="text-danger">*</span></label>
        <input type="url" name="link_url" id="{{ $prefix }}-inputLinkUrl"
            class="form-control" placeholder="https://drive.google.com/...">
        <div class="form-text text-muted">Pastikan link dapat diakses publik (Anyone with the link).</div>
    </div>
</div>
