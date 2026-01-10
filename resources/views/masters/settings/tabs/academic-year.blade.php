<div class="tab-pane fade" id="navs-pills-top-tahun" role="tabpanel">
    <div class="card mb-4">
        <h5 class="card-header">Kelola Tahun Ajaran</h5>
        <div class="card-body">
            <div id="activeYearAlert"></div>
            <form id="formAcademicYear" class="row g-3 align-items-end">
                @csrf
                <input type="hidden" id="year_id" name="id">
                <div class="col-md-5">
                    <label class="form-label">Tahun Ajaran</label>
                    <input type="text" class="form-control" name="year" id="year" placeholder="Contoh: 2025/2026"
                        required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Semester</label>
                    <select class="form-select" name="semester" id="semester" required>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100" id="btnSubmitYear">Simpan</button>
                </div>
            </form>
        </div>
        <hr class="m-0">
        <div class="card-header">
            <h5 class="mb-0">Riwayat Tahun Ajaran</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableAcademicYear">
                    <tr>
                        <td colspan="4" class="text-center">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>