@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master Tarif</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Master Tarif</li>
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
                    <h3 class="card-title mb-0">Daftar Tarif & Tindakan</h3>
                    <button class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal"
                        data-bs-target="#modalTambahTarif">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Tarif
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle w-100"
                            id="tableMasterTarif">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">#</th>
                                    <th>Nama Tindakan / Layanan</th>
                                    <th style="width: 180px;" class="text-end">Tarif / Harga</th>
                                    <th style="width: 150px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tarifs as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong class="text-dark d-block">{{ $item->nama_tindakan }}</strong>

                                            <div class="mt-1" style="font-size: 0.8rem;">
                                                @if (!empty($item->kategori))
                                                    <span class="badge bg-secondary me-1">
                                                        <i class="bi bi-tag-fill me-1"></i>{{ $item->kategori }}
                                                    </span>
                                                @endif

                                                @if (!empty($item->icd9_code))
                                                    <span class="badge bg-info text-dark"
                                                        title="{{ $item->icd9_display ?? '' }}">
                                                        <i class="bi bi-file-medical me-1"></i>ICD-9: {{ $item->icd9_code }}
                                                        @if (!empty($item->icd9_display))
                                                            - {{ $item->icd9_display }}
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            Rp {{ number_format($item->harga ?? ($item->tarif ?? 0), 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-warning btn-sm text-white me-1 btn-edit"
                                                data-id="{{ $item->id }}" data-nama="{{ $item->nama_tindakan }}"
                                                data-kategori="{{ $item->kategori ?? '' }}"
                                                data-icd9="{{ $item->icd9_code ?? '' }}"
                                                data-icd9display="{{ $item->icd9_display ?? '' }}"
                                                data-harga="{{ $item->harga ?? ($item->tarif ?? 0) }}" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('master-tarif.destroy', $item->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data tarif ini?')">
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

    <!-- Modal Tambah Tarif -->
    <div class="modal fade" id="modalTambahTarif" tabindex="-1" aria-labelledby="modalTambahTarifLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTambahTarifLabel">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Master Tarif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('master-tarif.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_tindakan" class="form-label required">Nama Tindakan / Layanan</label>
                                <input type="text" class="form-control" id="nama_tindakan" name="nama_tindakan"
                                    placeholder="Contoh: Konsultasi Dokter Umum" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="kategori" name="kategori"
                                    placeholder="Contoh: Rawat Jalan / Tindakan Medis">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="select_icd9" class="form-label">Cari ICD-9 (Kode / Deskripsi)</label>
                                <select class="form-select" id="select_icd9" style="width: 100%;"></select>

                                <input type="hidden" name="icd9_code" id="icd9_code">
                                <input type="hidden" name="icd9_display" id="icd9_display">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="harga" class="form-label required">Tarif / Harga (Rp)</label>
                            <input type="number" class="form-control" id="harga" name="harga"
                                placeholder="Contoh: 50000" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditTarif" tabindex="-1" aria-labelledby="modalEditTarifLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalEditTarifLabel">
                        <i class="fas fa-edit me-1"></i> Edit Master Tarif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Action dikosongkan karena di-set via jQuery -->
                <form id="formEditTarif" action="#" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_nama_tindakan" class="form-label required">Nama Tindakan /
                                    Layanan</label>
                                <input type="text" class="form-control" id="edit_nama_tindakan" name="nama_tindakan"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_kategori" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="edit_kategori" name="kategori">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_icd9_code" class="form-label">Kode ICD-9</label>
                                <input type="text" class="form-control" id="edit_icd9_code" name="icd9_code">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_icd9_display" class="form-label">Display / Deskripsi ICD-9</label>
                                <input type="text" class="form-control" id="edit_icd9_display" name="icd9_display">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_harga" class="form-label required">Tarif / Harga (Rp)</label>
                            <input type="number" class="form-control" id="edit_harga" name="harga" min="0"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-white">
                            <i class="fas fa-sync-alt me-1"></i> Update Data
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

            // Edit Button Handler
            $('#tableMasterTarif').on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const kategori = $(this).data('kategori');
                const icd9 = $(this).data('icd9');
                const icd9display = $(this).data('icd9display');
                const harga = $(this).data('harga');

                const updateUrl = `{{ url('master-tarif') }}/${id}`;
                $('#formEditTarif').attr('action', updateUrl);
                $('#edit_nama_tindakan').val(nama);
                $('#edit_kategori').val(kategori);
                $('#edit_icd9_code').val(icd9);
                $('#edit_icd9_display').val(icd9display);
                $('#edit_harga').val(harga);

                const modal = new bootstrap.Modal(document.getElementById('modalEditTarif'));
                modal.show();
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
    </script>
@endsection
