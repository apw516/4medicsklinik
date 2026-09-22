@extends('Template.Main')

@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Detail Akun</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Akun</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                {{-- Card Profil --}}
                <div class="col-md-5 col-lg-4 mb-3">
                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-body box-profile text-center">
                            <div class="mb-3">
                                <img class="profile-user-img img-fluid img-circle rounded-circle border p-1"
                                    src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama ?? auth()->user()->username) }}&background=0D6EFD&color=fff&size=128"
                                    alt="User Profile Picture" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>

                            <h4 class="profile-username fw-bold mb-0">{{ auth()->user()->nama ?? '-' }}</h4>
                            <p class="text-muted mb-2">{{ auth()->user()->username }}</p>

                            <ul class="list-group list-group-flush text-start my-3" style="font-size: 0.9rem;">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="fw-semibold"><i class="fas fa-shield-alt text-primary me-2"></i>Hak
                                        Akses</span>
                                    <span class="badge bg-info text-dark">
                                        {{ auth()->user()->hak_akses == 1 ? 'SUPER ADMIN' : 'USER' }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="fw-semibold"><i class="fas fa-id-card text-primary me-2"></i>IHS
                                        Number</span>
                                    <span class="font-monospace fw-bold">{{ auth()->user()->ihs_number ?? '-' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span class="fw-semibold"><i
                                            class="fas fa-check-circle text-primary me-2"></i>Status</span>
                                    @if (auth()->user()->status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Form Ubah Password --}}
                <div class="col-md-7 col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 fw-bold"><i class="fas fa-key text-warning me-2"></i>Ubah Password
                            </h5>
                        </div>
                        <div class="card-body">
                            <form id="formUpdatePassword">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="current_password" class="form-label fw-semibold">Password Saat Ini</label>
                                    <input type="password" class="form-control" id="current_password"
                                        name="current_password" required placeholder="Masukkan password lama">
                                    <div class="invalid-feedback" id="err-current_password"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Password Baru</label>
                                    <input type="password" class="form-control" id="password" name="password" required
                                        placeholder="Minimal 8 karakter">
                                    <div class="invalid-feedback" id="err-password"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password
                                        Baru</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required placeholder="Ulangi password baru">
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary" id="btn-save-password">
                                        <i class="fas fa-save me-1"></i> Simpan Password Baru
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#formUpdatePassword').on('submit', function(e) {
                e.preventDefault();
                // Reset state error
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#btn-save-password').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');
                $.ajax({
                    url: "{{ route('profile.update-password') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#btn-save-password').prop('disabled', false).html(
                            '<i class="fas fa-save me-1"></i> Simpan Password Baru');
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            $('#formUpdatePassword')[0].reset();
                        }
                    },
                    error: function(xhr) {
                        $('#btn-save-password').prop('disabled', false).html(
                            '<i class="fas fa-save me-1"></i> Simpan Password Baru');

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#err-${key}`).text(value[0]);
                            });
                        } else {
                            let errMessage = xhr.responseJSON?.message ||
                                'Terjadi kesalahan saat memperbarui password.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: errMessage
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection


