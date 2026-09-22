@extends('Template.Main')

@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master Practitioner</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">SATUSEHAT</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Practitioner</li>
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
                    <h3 class="card-title fw-bold">Data Practitioner</h3>
                    <div class="card-tools ms-auto">
                        <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal"
                            data-bs-target="#modalTambahOrganization">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Practitioner
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle" id="table-practitioner"
                            style="font-size: 0.85rem;">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">ID Practitioner</th>
                                    <th style="width: 15%;">NIK</th>
                                    <th style="width: 20%;">Nama Practitioner</th>
                                    <th style="width: 15%;">Jabatan</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 15%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dokter ?? [] as $index => $row)
                                    <tr id="row-{{ $row->id }}">
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="font-monospace text-center fw-bold">
                                            {{ $row->ihs_number ?? '-' }}
                                        </td>
                                        <td>{{ $row->nik }}</td>
                                        <td>{{ $row->nama_dokter }}</td>
                                        <td>{{ $row->jabatan ?? 'Dokter' }}</td>
                                        <td class="text-center">
                                            @if ($row->is_active == '1')
                                                <span class="badge bg-success px-2 py-1">Active</span>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                {{-- Tombol Edit / Sync SATUSEHAT --}}
                                                <button type="button" class="btn btn-sm btn-warning btn-edit"
                                                    data-id="{{ $row->id }}" data-nik="{{ $row->nik }}"
                                                    data-nama="{{ $row->nama_dokter }}" data-jabatan="{{ $row->jabatan }}"
                                                    data-status="{{ $row->is_active }}"
                                                    data-ihsnumber="{{ $row->ihs_number }}" title="Edit / Sync">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>

                                                {{-- Tombol Hapus --}}
                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-id="{{ $row->id }}" data-nama="{{ $row->nama_dokter }}"
                                                    title="Hapus">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL TAMBAH ORGANIZATION (BOOTSTRAP 5) -->
    <div class="modal fade" id="modalTambahOrganization" tabindex="-1" aria-labelledby="modalTambahOrganizationLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTambahOrganizationLabel">
                        <i class="fas fa-sitemap me-1"></i> Tambah Practitioner
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="form-tambah-practitioner" method="POST" action="{{ route('practitioner.store') }}">
                    @csrf
                    <div class="modal-body" style="font-size: 0.9rem;">
                        <!-- NAMA ORGANISASI / DEPARTEMEN -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nik" placeholder="Masukan nomor KTP ..."
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">IHS NUMBER <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="ihs_number"
                                placeholder="Masukan kode IHS yang didapat dari satu sehat ..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">NAMA LENGKAP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_dokter"
                                placeholder="Masukan Nama Lengkap ..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">JABATAN <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="jabatan"
                                placeholder="Contoh: Dokter / Apoteker / Perawat" required>
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold" id="btn-save-org">
                            <i class="fas fa-paper-plane me-1"></i> Simpan Practitioner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- MODAL CREATE LOCATION -->
    <div class="modal fade" id="modalTambahLocation" tabindex="-1" aria-labelledby="modalTambahLocationLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalTambahLocationLabel">
                        <i class="fas fa-map-marker-alt me-1"></i> Tambah Lokasi / Ruangan Fisik
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="form-tambah-location" method="POST" action="{{ route('location.store') }}">
                    @csrf
                    <div class="modal-body" style="font-size: 0.9rem;">

                        <!-- UNIT PENANGGUNG JAWAB (MANAGING ORGANIZATION) -->
                        <div class="mb-3 bg-light p-2 rounded border">
                            <label class="form-label fw-bold text-secondary mb-1">Unit / Organization Pengelola:</label>
                            <input type="text" id="loc_managing_org_name"
                                class="form-control-plaintext fw-bold text-primary px-2" readonly value="-">
                            <input type="hidden" name="managing_organization_id" id="loc_managing_org_id">
                        </div>

                        <!-- KODE LOCATION (OPSIONAL) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Location ID SATUSEHAT (Opsional)</label>
                            <input type="text" class="form-control" name="satusehat_location_id"
                                placeholder="Isi jika ruangan sudah terdaftar di SATUSEHAT">
                            <div class="form-text text-muted">*Jika diisi, sistem tidak akan mengirim ulang request ke API
                                SATUSEHAT.</div>
                        </div>

                        <!-- NAMA RUANGAN / LOKASI -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Ruangan / Fisik Lokasi <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name"
                                placeholder="Contoh: Ruang Periksa Poli Dalam 01 / Bed 02 ICU" required>
                        </div>

                        <div class="row">
                            <!-- PHYSICAL TYPE -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tipe Fisik Lokasi <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" name="physical_type" required>
                                    <option value="ro">Ruangan (Room)</option>
                                    <option value="bd">Tempat Tidur (Bed)</option>
                                    <option value="bu">Gedung (Building)</option>
                                    <option value="wi">Sayap / Area (Wing)</option>
                                    <option value="ve">Kendaraan / Ambulans (Vehicle)</option>
                                </select>
                            </div>
                            <!-- STATUS LOKASI -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status Lokasi</label>
                                <select class="form-select" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- DESKRIPSI TAMBAHAN -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi Lokasi</label>
                            <textarea class="form-control" name="description" rows="2"
                                placeholder="Contoh: Gedung A Lantai 2 Sayap Timur"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold" id="btn-save-location">
                            <i class="fas fa-paper-plane me-1"></i> Simpan & Kirim Location
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Modal Edit Practitioner --}}
    <div class="modal fade" id="modalEditPractitioner" tabindex="-1" aria-labelledby="modalEditLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel">Edit Practitioner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditPractitioner">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nik" class="form-label fw-bold">IHS Number</label>
                            <input type="text" class="form-control" id="edit_ihs_number" name="ihs_number"
                                maxlength="16">
                        </div>
                        <div class="mb-3">
                            <label for="edit_nik" class="form-label fw-bold">NIK</label>
                            <input type="text" class="form-control" id="edit_nik" name="nik" required
                                maxlength="16">
                        </div>
                        <div class="mb-3">
                            <label for="edit_nama" class="form-label fw-bold">Nama Practitioner</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama_dokter" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_jabatan" class="form-label fw-bold">Jabatan</label>
                            <input type="text" class="form-control" id="edit_jabatan" name="jabatan">
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label fw-bold">Status</label>
                            <select class="form-select" id="edit_status" name="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-update">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            if (!$.fn.DataTable.isDataTable('#tableRiwayatPembayaran')) {
                $('#table-organization').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                    },
                    "pageLength": 10,
                    "ordering": true
                });
            }
        });
        $(document).ready(function() {
            // Handle Submit Form via AJAX ke API Bridging SATUSEHAT
            $('#form-tambah-practitioner').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let btn = $('#btn-save-org');
                $.ajax({
                    url: form.attr('action'),
                    type: "POST",
                    data: form.serialize(),
                    beforeSend: function() {
                        btn.prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin me-1"></i> Mengirim ke Kemenkes...'
                        );
                    },
                    success: function(response) {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-paper-plane me-1"></i> Kirim ke SATUSEHAT'
                        );

                        if (response.status) {
                            alert('Data berhasil disimpan ...');

                            // Close modal Bootstrap 5
                            let modalEl = document.getElementById('modalTambahOrganization');
                            let modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();

                            location.reload();
                        } else {
                            alert('Gagal: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-paper-plane me-1"></i> Kirim ke SATUSEHAT'
                        );
                        alert('Terjadi kesalahan sistem / koneksi API SATUSEHAT.');
                    }
                });
            });
        });
        $(document).ready(function() {
            // Event Handler saat tombol Create Location diklik
            $(document).on('click', '.btn-create-location', function() {
                let orgId = $(this).data('org-id');
                let orgName = $(this).data('org-name');
                if (!orgId) {
                    alert(
                        'Unit ini belum memiliki Organization ID SATUSEHAT! Silakan sync/bridging unit terlebih dahulu.'
                    );
                    return;
                }

                // Set value ke input modal
                $('#loc_managing_org_id').val(orgId);
                $('#loc_managing_org_name').val(orgName + ' (ID: ' + orgId + ')');

                // Tampilkan Modal
                let modalLoc = new bootstrap.Modal(document.getElementById('modalTambahLocation'));
                modalLoc.show();
            });

            // AJAX Submit Form Location
            $('#form-tambah-location').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let btn = $('#btn-save-location');

                $.ajax({
                    url: form.attr('action'),
                    type: "POST",
                    data: form.serialize(),
                    beforeSend: function() {
                        btn.prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');
                    },
                    success: function(response) {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-paper-plane me-1"></i> Simpan & Kirim Location'
                        );

                        if (response.status) {
                            alert('Berhasil! Location tersimpan.');

                            let modalEl = document.getElementById('modalTambahLocation');
                            let modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();

                            location.reload();
                        } else {
                            alert('Gagal: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-paper-plane me-1"></i> Simpan & Kirim Location'
                        );
                        alert('Terjadi kesalahan saat menyimpan data Location.');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Setup CSRF Token untuk AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // ----------------------------------------------------
            // 1. PROSES BUKA MODAL EDIT & SET DATA
            // ----------------------------------------------------
            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                let nik = $(this).data('nik');
                let nama = $(this).data('nama');
                let jabatan = $(this).data('jabatan');
                let status = $(this).data('status');
                let ihs_number = $(this).data('ihsnumber');
                $('#edit_id').val(id);
                $('#edit_nik').val(nik);
                $('#edit_nama').val(nama);
                $('#edit_jabatan').val(jabatan ?? 'Dokter');
                $('#edit_status').val(status);
                $('#edit_ihs_number').val(ihs_number);

                $('#modalEditPractitioner').modal('show');
            });

            // ----------------------------------------------------
            // 2. SUBMIT UPDATE DATA VIA AJAX
            // ----------------------------------------------------
            $('#formEditPractitioner').on('submit', function(e) {
                e.preventDefault();

                let id = $('#edit_id').val();
                let url = "{{ route('satusehat.practitioner.update', ':id') }}".replace(':id', id);

                $('#btn-save-update').prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: url,
                    type: 'POST', // Menggunakan POST dengan _method PUT
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#modalEditPractitioner').modal('hide');
                        $('#btn-save-update').prop('disabled', false).text('Simpan Perubahan');

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location
                                    .reload(); // Reload halaman untuk pembaruan data
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#btn-save-update').prop('disabled', false).text('Simpan Perubahan');
                        let errMessage = xhr.responseJSON?.message ||
                            'Terjadi kesalahan saat memperbarui data.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: errMessage
                        });
                    }
                });
            });

            // ----------------------------------------------------
            // 3. PROSES DELETE DATA VIA AJAX
            // ----------------------------------------------------
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                let nama = $(this).data('nama');
                let url = "{{ route('satusehat.practitioner.destroy', ':id') }}".replace(':id', id);
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: `Data Practitioner "${nama}" akan dihapus!`,
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
                            type: 'POST', // Menggunakan POST dengan _method PUT
                            data: {
                                _token: "{{ csrf_token() }}",
                                id
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert('data berhasil dihapus')
                                    location.reload()
                                }
                            },
                            error: function(xhr) {

                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
