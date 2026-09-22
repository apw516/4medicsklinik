@extends('Template.Main')

@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master Organisasi</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">SATUSEHAT</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Organization</li>
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
                    <h3 class="card-title fw-bold">Data Organisasi</h3>
                    <div class="card-tools ms-auto">
                        <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal"
                            data-bs-target="#modalTambahOrganization">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Organisasi
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle" id="table-organization"
                            style="font-size: 0.85rem;">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 20%;">Unit ID (SATUSEHAT)</th>
                                    <th style="width: 20%;">Nama Unit</th>
                                    <th style="width: 15%;">Tipe (Part Of)</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 30%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($organizations ?? [] as $index => $row)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="font-monospace text-center fw-bold">
                                            {{ $row->satusehat_org_id ?? '-' }}
                                        </td>
                                        <td>{{ $row->nama_organisasi }}</td>
                                        <td>{{ $row->tipe ?? 'Faskes Utama' }}</td>
                                        <td class="text-center">
                                            @if ($row->status == 'active')
                                                <span class="badge bg-success px-2 py-1">Active</span>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <!-- TOMBOL CREATE LOCATION (MEMBAWA DATA UNIT) -->
                                            <button class="btn btn-primary btn-sm btn-create-location me-1"
                                                data-org-id="{{ $row->satusehat_org_id }}"
                                                data-org-name="{{ $row->nama_organisasi }}"
                                                {{ empty($row->satusehat_org_id) ? 'disabled title="Unit harus dibridging ke SATUSEHAT dulu"' : '' }}>
                                                <i class="bi bi-house-add-fill"></i>
                                            </button>
                                            <form action="{{ route('master-organisasi.destroy', $row->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data unit ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                    <i class="bi bi-folder-x"></i>
                                                </button>
                                            </form>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">
                                            Belum ada data organisasi yang dimapping ke SATUSEHAT.
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
                        <i class="fas fa-sitemap me-1"></i> Tambah Sub-Organisasi SATUSEHAT
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="form-tambah-organization" method="POST" action="{{ route('organization.store') }}">
                    @csrf
                    <div class="modal-body" style="font-size: 0.9rem;">
                        <div hidden class="mb-3">
                            <label class="form-label fw-bold">Parent Organization ID (Org ID RS/Faskes Utama) <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="part_of_id"
                                placeholder="Contoh: 1000001 (Org ID RS dari Kemenkes)"
                                value="a9256dc3-ca8e-4f7c-a167-ea980e4446f0" readonly>
                            <div class="form-text">ID Organisasi induk yang didapat dari akun IHS SATUSEHAT Faskes.</div>
                        </div>

                        <!-- NAMA ORGANISASI / DEPARTEMEN -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Sub-Organisasi / Unit <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name"
                                placeholder="Contoh: Poliklinik Penyakit Dalam / Instalasi Laboratorium" required>
                        </div>

                        <div class="row">
                            <!-- KONTAK TELEPON -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">No. Telepon Unit</label>
                                <input type="text" class="form-control" name="phone" placeholder="0231-xxxxxx">
                            </div>
                            <!-- EMAIL -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email Unit</label>
                                <input type="email" class="form-control" name="email" placeholder="lab@rsudwaled.id">
                            </div>
                        </div>

                        <!-- ALAMAT DETAIL -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat Gedung / Lokasi Unit</label>
                            <textarea class="form-control" name="address" rows="2" placeholder="Jl. Raya Waled No. 22..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Kode Pos</label>
                                <input type="text" class="form-control" name="postal_code" placeholder="45187">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status Organisasi</label>
                                <select class="form-select" name="active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tipe</label>
                                <select class="form-select" name="tipe">
                                    <option value="dept">Hospital Department</option>
                                    <option value="prov">Healthcare Provider</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold" id="btn-save-org">
                            <i class="fas fa-paper-plane me-1"></i> Kirim ke SATUSEHAT
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
            $('#form-tambah-organization').on('submit', function(e) {
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
                            alert('Berhasil! Organization ID: ' + response.organization_id);

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
@endsection
