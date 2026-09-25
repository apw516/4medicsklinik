<!doctype html>
<html lang="id">

<head>
    <title>4medics Klinik </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('public/login/css/style.css') }}">

    <style>
        /* Custom Warna Tombol Merah Pudar / Soft Red */
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
    </style>
</head>

<body>
    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center mb-5">
                    <h2 class="heading-section">Silahkan Login</h2>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-7 col-lg-5">
                    <div class="wrap">
                        <div class="img" style="background-image: url({{ asset('public/login/images/bg-2.jpg') }});">
                        </div>
                        <div class="login-wrap p-4 p-md-5">

                            <!-- ALERT NOTIFIKASI RESPONSE CONTROLLER -->
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <small>{{ session('success') }}</small>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <small>{{ session('error') }}</small>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0 pl-3">
                                        @foreach ($errors->all() as $error)
                                            <li><small>{{ $error }}</small></li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <!-- END ALERT NOTIFIKASI -->

                            <div class="d-flex">
                                <div class="w-100">
                                    <h3 class="mb-4">Login</h3>
                                </div>
                            </div>

                            <form action="{{ route('login.post') }}" method="POST" class="signin-form">
                                @csrf
                                <div class="form-group mt-3">
                                    <input type="text" name="username" class="form-control"
                                        value="{{ old('username') }}" required>
                                    <label class="form-control-placeholder" for="username">Username</label>
                                </div>
                                <div class="form-group">
                                    <input id="password-field" type="password" name="password" class="form-control"
                                        required>
                                    <label class="form-control-placeholder" for="password">Password</label>
                                    <span toggle="#password-field"
                                        class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                </div>
                                <div class="form-group">
                                    <!-- KELAS DIUBAH MENJADI btn-soft-red -->
                                    <button type="submit"
                                        class="form-control btn btn-soft-red rounded submit px-3">Sign
                                        In</button>
                                </div>
                            </form>

                            <p class="text-center">
                                Tidak Punya Akun ? <a href="{{ route('registrasi') }}"
                                    style="color: #d9534f;">Registrasi</a>
                            </p>
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
