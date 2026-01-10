@extends('layouts.admin')

@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card mb-4">
                <h5 class="card-header">Detail Profil</h5>
                <!-- Account -->
                <div class="card-body">
                    <form id="formAccountSettings" method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Informasi Akun Dasar -->
                            <div class="col-md-12 mb-4">
                                <h6 class="fw-bold">Informasi Akun</h6>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="firstName" class="form-label">Nama Lengkap</label>
                                <input class="form-control" type="text" id="firstName" name="name"
                                    value="{{ old('name', $user->name) }}" autofocus />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input class="form-control" type="text" id="email" name="email"
                                    value="{{ old('email', $user->email) }}" />
                            </div>

                            <!-- Ganti Password -->
                            <div class="col-md-12 my-3">
                                <h6 class="fw-bold text-warning"><i class="bx bx-lock-alt me-1"></i> Ganti Password
                                    (Opsional)</h6>
                                <small class="text-muted d-block mb-3">Kosongkan jika tidak ingin mengubah password.</small>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="password" class="form-label">Password Baru</label>
                                <input class="form-control" type="password" id="password" name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <input class="form-control" type="password" id="password_confirmation"
                                    name="password_confirmation"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                            </div>

                            <!-- Informasi Khusus Guru -->
                            @if($user->role === 'teacher')
                                <div class="col-md-12 my-3">
                                    <hr>
                                    <h6 class="fw-bold text-primary"><i class="bx bx-id-card me-1"></i> Data Guru</h6>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="nip" class="form-label">NIP</label>
                                    <input class="form-control" type="text" id="nip" name="nip"
                                        value="{{ old('nip', $teacher->nip ?? '') }}" />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="phone" class="form-label">No. Telepon</label>
                                    <input class="form-control" type="text" id="phone" name="phone"
                                        value="{{ old('phone', $teacher->phone ?? '') }}" />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="gender" class="form-label">Jenis Kelamin</label>
                                    <select id="gender" name="gender" class="form-select">
                                        <option value="L" {{ (old('gender', $teacher->gender ?? '') == 'L') ? 'selected' : '' }}>
                                            Laki-laki</option>
                                        <option value="P" {{ (old('gender', $teacher->gender ?? '') == 'P') ? 'selected' : '' }}>
                                            Perempuan</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="address" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="address" name="address"
                                        rows="3">{{ old('address', $teacher->address ?? '') }}</textarea>
                                </div>
                            @endif
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                            <button type="reset" class="btn btn-outline-secondary">Batal</button>
                        </div>
                    </form>
                </div>
                <!-- /Account -->
            </div>
        </div>
    </div>
@endsection