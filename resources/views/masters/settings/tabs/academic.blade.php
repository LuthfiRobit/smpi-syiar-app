<div class="tab-pane fade" id="navs-pills-top-akademik" role="tabpanel">
    <!-- Academic Year Selector for Context -->
    <div class="card mb-4 bg-label-secondary">
        <div class="card-body d-flex align-items-center justify-content-between p-3">
            <div>
                <h6 class="mb-0 text-dark"><i class="bx bx-info-circle me-1"></i> Kontext Tahun Ajaran</h6>
                <small class="text-muted">Pilih tahun ajaran untuk mengatur hari aktif dan libur.</small>
            </div>
            <div style="min-width: 250px;">
                <select id="selectAcademicYearSettings" class="form-select form-select-lg fw-bold">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>
                            {{ $year->name }} {{ $year->semester }} {{ $year->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Hari Aktif -->
        <div class="col-md-5">
            <div class="card mb-4">
                <h5 class="card-header">Hari Aktif Sekolah</h5>
                <div class="card-body">
                    <form id="formActiveDays">
                        @csrf
                        <input type="hidden" name="academic_year_id" id="settings_active_days_year_id">
                        <div class="mb-3">
                            <div class="form-check mt-3">
                                <input class="form-check-input active-day-check" type="checkbox" name="active_days[]"
                                    value="Senin" id="daySenin">
                                <label class="form-check-label" for="daySenin">Senin</label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input active-day-check" type="checkbox" name="active_days[]"
                                    value="Selasa" id="daySelasa">
                                <label class="form-check-label" for="daySelasa">Selasa</label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input active-day-check" type="checkbox" name="active_days[]"
                                    value="Rabu" id="dayRabu">
                                <label class="form-check-label" for="dayRabu">Rabu</label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input active-day-check" type="checkbox" name="active_days[]"
                                    value="Kamis" id="dayKamis">
                                <label class="form-check-label" for="dayKamis">Kamis</label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input active-day-check" type="checkbox" name="active_days[]"
                                    value="Jumat" id="dayJumat">
                                <label class="form-check-label" for="dayJumat">Jumat</label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input active-day-check" type="checkbox" name="active_days[]"
                                    value="Sabtu" id="daySabtu">
                                <label class="form-check-label" for="daySabtu">Sabtu</label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input active-day-check" type="checkbox" name="active_days[]"
                                    value="Minggu" id="dayMinggu">
                                <label class="form-check-label" for="dayMinggu">Minggu</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan Hari Aktif</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Hari Libur -->
        <div class="col-md-7">
            <div class="card mb-4">
                <h5 class="card-header">Hari Libur Akademik</h5>
                <div class="card-body">
                    <form id="formHoliday" class="mb-4">
                        @csrf
                        <input type="hidden" name="academic_year_id" id="settings_holiday_year_id">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="name"
                                    placeholder="Nama Libur (e.g. Libur Semester)" required>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" name="end_date" required>
                            </div>
                            <div class="col-md-12 d-flex align-items-center justify-content-between mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_recurring" value="1"
                                        id="isRecurring">
                                    <label class="form-check-label" for="isRecurring">Berulang Tiap
                                        Tahun</label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Tambah</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive" style="max-height: 300px; overflow-y:auto;">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableHolidays">
                                <tr>
                                    <td colspan="4" class="text-center">Memuat...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>