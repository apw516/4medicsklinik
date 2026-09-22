<!doctype html>
<html lang="id">

<head>
    <title>Klinik Pratama H. Farid Medika </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('public/login/css/style.css') }}">

    <style>
        /* Custom Warna Merah Pudar / Soft Red */
        .btn-soft-red {
            background-color: #d9534f !important;
            border-color: #d9534f !important;
            color: #ffffff !important;
            transition: all 0.3s ease;
        }

        .btn-soft-red:hover,
        .btn-soft-red:focus {
            background-color: #c9302c !important;
            border-color: #c9302c !important;
            color: #ffffff !important;
            box-shadow: 0 4px 10px rgba(217, 83, 79, 0.3);
        }

        /* Warna Tautan Login */
        .link-soft-red {
            color: #d9534f !important;
        }

        .link-soft-red:hover {
            color: #c9302c !important;
        }
    </style>
</head>

<body>
    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center mb-4">
                    <h2 class="heading-section">Buat Akun Baru</h2>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-7 col-lg-5">
                    <div class="wrap">
                        <div class="img" style="background-image: url({{ asset('public/login/images/bg-2.jpg') }});">
                        </div>
                        <div class="login-wrap p-4 p-md-5">
                            <div class="d-flex mb-3">
                                <div class="w-100">
                                    <h3 class="mb-2">Registrasi</h3>
                                </div>
                            </div>
                            <form action="{{ route('registrasi.store') }}" method="POST" class="signup-form">
                                @csrf

                                <!-- Nama Lengkap -->
                                <div class="form-group mt-3">
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="{{ old('name') }}" required>
                                    <label class="form-control-placeholder" for="name">Nama Lengkap</label>
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Username -->
                                <div class="form-group">
                                    <input type="text" id="username" name="username" class="form-control"
                                        value="{{ old('username') }}" required>
                                    <label class="form-control-placeholder" for="username">Username</label>
                                    @error('username')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="form-group">
                                    <input id="password-field" type="password" name="password" class="form-control"
                                        required>
                                    <label class="form-control-placeholder" for="password-field">Password</label>
                                    <span toggle="#password-field"
                                        class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Konfirmasi Password -->
                                <div class="form-group">
                                    <input id="password-confirm-field" type="password" name="password_confirmation"
                                        class="form-control" required>
                                    <label class="form-control-placeholder" for="password-confirm-field">Konfirmasi
                                        Password</label>
                                    <span toggle="#password-confirm-field"
                                        class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                </div>

                                <!-- Tombol Submit -->
                                <div class="form-group mt-4">
                                    <button type="submit"
                                        class="form-control btn btn-soft-red rounded submit px-3">Daftar
                                        Sekarang</button>
                                </div>
                            </form>

                            <p class="text-center">Sudah Punya Akun? <a href="{{ route('login') }}"
                                    class="link-soft-red">Login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('public/login/js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/login/js/popper.js') }}"></script>
    <script src="{{ asset('public/login/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/login/js/main.js') }}"></script>
</body>

</html>
