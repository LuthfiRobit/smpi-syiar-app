<div class="tab-pane fade show active" id="navs-pills-top-umum" role="tabpanel">
    <div class="card mb-4">
        <h5 class="card-header">Identitas Sekolah</h5>
        <div class="card-body">
            <form id="formIdentity" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-center mb-4">
                    <div class="col-md-2 text-center">
                        <div class="flex-shrink-0">
                            <img src="{{ $school_identity && $school_identity->logo_path ? asset('storage/' . $school_identity->logo_path) : asset('sekolah/assets/img/illustrations/page-pricing-enterprise.png') }}"
                                alt="logo-sekolah" class="d-block rounded mb-2" height="100" width="100"
                                id="logoPreview" style="object-fit: contain;" />
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-xs btn-primary me-2 mb-2" tabindex="0">
                                    <span class="d-none d-sm-block">Upload Logo</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                    <input type="file" id="upload" name="logo" class="account-file-input" hidden
                                        accept="image/png, image/jpeg" />
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="mb-3 col-md-8">
                                <label for="name" class="form-label">Nama Sekolah</label>
                                <input class="form-control" type="text" id="name" name="name" placeholder="Nama Sekolah"
                                    required />
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="npsn" class="form-label">NPSN</label>
                                <input class="form-control" type="text" name="npsn" id="npsn" placeholder="NPSN"
                                    required />
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label for="email" class="form-label">E-mail Kontak</label>
                        <input class="form-control" type="email" id="email" name="email"
                            placeholder="admin@sekolah.sch.id" required />
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Contoh: 021-123456"
                            required />
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="website" class="form-label">Website</label>
                        <input type="text" class="form-control" id="website" name="website"
                            placeholder="www.sekolah.sch.id" />
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="headmaster_name" class="form-label">Nama Kepala Sekolah</label>
                        <input type="text" class="form-control" id="headmaster_name" name="headmaster_name"
                            placeholder="Nama Lengkap Beserta Gelar" required />
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="headmaster_nip" class="form-label">NIP Kepala Sekolah</label>
                        <input type="text" class="form-control" id="headmaster_nip" name="headmaster_nip"
                            placeholder="NIP Kepala Sekolah" />
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="address" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required
                            placeholder="Alamat lengkap sekolah..."></textarea>
                    </div>
                </div>
                <div class="mt-2">
                    <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>