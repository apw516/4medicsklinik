@extends('Template.Main')

@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master User</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">SATUSEHAT</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Master User</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <!-- MAIN CARD -->
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold">Data User</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle" id="table-practitioner"
                            style="font-size: 0.85rem;">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">Nama</th>
                                    <th style="width: 15%;">Username</th>
                                    <th style="width: 20%;">Hak Akses</th>
                                    <th style="width: 15%;">Ihs Number</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user ?? [] as $index =>$row)
                                    <tr id="row-{{ $row->id }}">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="fw-bold">{{ $row->nama ?? '-' }}</td>
                                        <td>{{ $row->username }}</td>
                                        <td>
                                            @if ($row->hak_akses == 1)
                                                <span class="badge bg-info text-dark">SUPER ADMIN</span>
                                            @else
                                                <span class="badge bg-light text-dark">USER</span>
                                            @endif
                                        </td>
                                        <td class="font-monospace text-center">{{ $row->ihs_number ?? '-' }}</td>
                                        <td class="text-center">
                                            @if ($row->status == '1')
                                                <span class="badge bg-success px-2 py-1">Active</span>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-warning btn-edit"
                                                    data-id="{{ $row->id }}" data-nama="{{ $row->nama }}"
                                                    data-username="{{ $row->username }}"
                                                    data-hak_akses="{{ $row->hak_akses }}"
                                                    data-ihs_number="{{ $row->ihs_number }}"
                                                    data-status="{{ $row->status }}" title="Edit Data">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-id="{{ $row->id }}" data-nama="{{ $row->nama }}"
                                                    title="Hapus Data">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">
                                            Belum ada data Practitioner yang dimapping ke SATUSEHAT.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Modal Edit User / Practitioner -->
                        <div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalEditUserLabel">Edit User Practitioner</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form id="formEditUser">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" id="edit_id" name="id">

                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="edit_nama" class="form-label fw-bold">Nama Lengkap</label>
                                                <input type="text" class="form-control form-control-sm" id="edit_nama"
                                                    name="nama" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_username" class="form-label fw-bold">Username</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="edit_username" name="username" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_hak_akses" class="form-label fw-bold">Hak Akses</label>
                                                <select class="form-select form-select-sm" id="edit_hak_akses"
                                                    name="hak_akses" required>
                                                    <option value="1">SUPER ADMIN</option>
                                                    <option value="2">USER / DOKTER</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_ihs_number" class="form-label fw-bold">IHS Number
                                                    (SATUSEHAT)</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="edit_ihs_number" name="ihs_number"
                                                    placeholder="Contoh: P12345678">
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_status" class="form-label fw-bold">Status</label>
                                                <select class="form-select form-select-sm" id="edit_status"
                                                    name="status" required>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                id="btn-save-update">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
            // 1. TAMPILKAN DATA PADA MODAL EDIT
            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                let nama = $(this).data('nama');
                let username = $(this).data('username');
                let hak_akses = $(this).data('hak_akses');
                let ihs_number = $(this).data('ihs_number');
                let status = $(this).data('status');

                $('#edit_id').val(id);
                $('#edit_nama').val(nama);
                $('#edit_username').val(username);
                $('#edit_hak_akses').val(hak_akses);
                $('#edit_ihs_number').val(ihs_number);
                $('#edit_status').val(status);

                $('#modalEditUser').modal('show');
            });

            // 2. PROSES UPDATE DATA VIA AJAX
            $('#formEditUser').on('submit', function(e) {
                e.preventDefault();
                let id = $('#edit_id').val();
                let url = "{{ route('user.practitioner.update', ':id') }}".replace(':id', id);
                $('#btn-save-update').prop('disabled', true).text('Menyimpan...');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#modalEditUser').modal('hide');
                        $('#btn-save-update').prop('disabled', false).text('Simpan Perubahan');

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#btn-save-update').prop('disabled', false).text('Simpan Perubahan');
                        let errMessage = xhr.responseJSON?.message || 'Gagal memperbarui data.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: errMessage
                        });
                    }
                });
            });
            // 3. PROSES DELETE DATA VIA AJAX (Mencegah CSRF Mismatch)
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                let nama = $(this).data('nama');
                let url = "{{ route('user.practitioner.destroy', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `User "${nama}" akan dihapus dari sistem!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });

                                    $(`#row-${id}`).fadeOut(400, function() {
                                        $(this).remove();
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errMessage = xhr.responseJSON?.message ||
                                    'Gagal menghapus data.';
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: errMessage
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
