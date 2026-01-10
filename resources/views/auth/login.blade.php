<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('sekolah/assets/') }}/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - {{ $school_identity->name ?? 'SIMS Sekolah' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('sekolah/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('sekolah/assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('sekolah/assets/vendor/css/core.css') }}"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('sekolah/assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('sekolah/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('sekolah/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('sekolah/assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('sekolah/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('sekolah/assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Content -->
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Login Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4">
                            <a href="{{ route('login') }}" class="app-brand-link gap-2">
                                @if($school_identity && $school_identity->logo_path)
                                    <img src="{{ asset('storage/' . $school_identity->logo_path) }}" alt="logo" height="40">
                                @endif
                                <span
                                    class="app-brand-text demo text-body fw-bolder fs-3">{{ $school_identity->name ?? 'SIMS Sekolah' }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->

                        <h4 class="mb-2">Selamat Datang! 👋</h4>
                        <p class="mb-4">Silahkan masuk menggunakan akun Anda</p>

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

                        <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="login" class="form-label">Email atau Username</label>
                                <input type="text" class="form-control @error('login') is-invalid @enderror" id="login"
                                    name="login" placeholder="Masukkan email atau username" value="{{ old('login') }}"
                                    autofocus />
                                @error('login')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember" />
                                    <label class="form-check-label" for="remember">Ingat Saya</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary d-grid w-100">Login</button>
                            </div>
                        </form>

                        <p class="text-center">
                            <span>Belum memiliki akun?</span>
                            <a href="javascript:void(0);">
                                <span>Hubungi Admin</span>
                            </a>
                        </p>
                    </div>
                </div>
                <!-- /Login Card -->
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Core JS -->
    <script src="{{ asset('sekolah/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sekolah/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sekolah/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sekolah/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('sekolah/assets/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('sekolah/assets/js/main.js') }}"></script>
</body>

</html>