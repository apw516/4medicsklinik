@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master Unit</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Master Unit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Daftar Nama Unit</h3>
                    <button class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal"
                        data-bs-target="#modalTambahLocation">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Unit
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle w-100"
                            id="tableMasterTarif">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Nama Unit</th>
                                    <th style="width: 180px;" class="text-end">Organization ID</th>
                                    <th style="width: 150px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($units as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong class="text-dark d-block">{{ $item->nama_lokasi }}</strong>
                                            <div class="mt-1" style="font-size: 0.8rem;">
                                                {{ $item->deskripsi }}
                                            </div>
                                        </td>
                                        <td>
                                            {{ $item->managing_organization_id }}
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('master-unit.destroy', $item->id) }}" method="POST"
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
                        <div class="mb-3">
                            <label class="form-label fw-bold">Unit / Organization Pengelola <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="managing_organization_id" id="loc_managing_org_id" required>
                                <option value="">-- Pilih Organization / Unit --</option>
                                @foreach ($organizations as $org)
                                    <option value="{{ $org->satusehat_org_id }}">{{ $org->nama_organisasi }}</option>
                                @endforeach
                            </select>
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
    <!-- Additional CSS fix for Select2 Z-Index in Bootstrap Modal -->
    <style>
        .select2-container--open {
            z-index: 9999 !important;
        }
    </style>

    <!-- JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // DataTable
            $('#tableMasterTarif').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    emptyTable: "Belum ada data tarif.",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Lanjut",
                        previous: "Kembali"
                    }
                }
            });


            // Inisialisasi Select2 SATU KALI saat document ready
            const $selectIcd9 = $('#select_icd9').select2({
                dropdownParent: $('#modalTambahTarif'),
                placeholder: 'Ketik kode atau deskripsi ICD-9...',
                allowClear: true,
                width: '100%',
                minimumInputLength: 2,
                ajax: {
                    url: "{{ route('icd9.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.diag,
                                    text: item.diag + ' - ' + item.nama_panjang,
                                    code: item.diag,
                                    display: item.nama_panjang
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            // Event saat item dipilih dari Select2
            $selectIcd9.on('select2:select', function(e) {
                var data = e.params.data;
                $('#icd9_code').val(data.code);
                $('#icd9_display').val(data.display);
            });

            $selectIcd9.on('select2:clear', function() {
                $('#icd9_code').val('');
                $('#icd9_display').val('');
            });

            // Reset saat modal ditutup
            $('#modalTambahTarif').on('hidden.bs.modal', function() {
                $selectIcd9.val(null).trigger('change');
                $('#icd9_code').val('');
                $('#icd9_display').val('');
            });
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
    </script>
@endsection
