@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master Obat</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Master Obat</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 fs-6 fw-bold">Daftar Obat</h3>
                    <button class="btn btn-primary btn-sm ms-auto py-1" data-bs-toggle="modal"
                        data-bs-target="#modalTambahObat">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Obat
                    </button>
                </div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <!-- Ditambahkan table-sm dan custom class table-custom-sm -->
                        <table
                            class="table table-sm table-bordered table-striped table-hover align-middle w-100 table-custom-sm mb-0"
                            id="tableMasterObat">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;" class="text-center">#</th>
                                    <th>Nama Obat</th>
                                    <th style="width: 130px;">Kategori</th>
                                    <th style="width: 100px;">Satuan</th>
                                    <th style="width: 110px;">KFA Code</th>
                                    <th>KFA Display</th>
                                    <th style="width: 90px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($obats as $index => $item)
                                    <tr>
                                        <td class="text-center fw-bold text-secondary">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $item->nama_obat }}</span>
                                        </td>
                                        <td>
                                            @if (!empty($item->kategori))
                                                <span class="badge bg-light text-dark border">{{ $item->kategori }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->satuan ?? '-' }}</td>
                                        <td>
                                            @if (!empty($item->kfa_code))
                                                <code class="text-primary font-monospace">{{ $item->kfa_code }}</code>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-truncate" style="max-width: 200px;"
                                            title="{{ $item->kfa_display }}">
                                            {{ $item->kfa_display ?? '-' }}
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-warning btn-xs btn-edit"
                                                    data-id="{{ $item->id }}" data-nama="{{ $item->nama_obat }}"
                                                    data-kategori="{{ $item->kategori ?? '' }}"
                                                    data-satuan="{{ $item->satuan ?? '' }}"
                                                    data-kfacode="{{ $item->kfa_code ?? '' }}"
                                                    data-kfadisplay="{{ $item->kfa_display ?? '' }}" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <form action="{{ route('master-obat.destroy', $item->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data obat ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-xs"
                                                        title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
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

    <!-- Modal Tambah Obat -->
    <div class="modal fade" id="modalTambahObat" tabindex="-1" aria-labelledby="modalTambahObatLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title fw-bold fs-6" id="modalTambahObatLabel">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Master Obat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('master-obat.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_obat" class="form-label small fw-bold">Nama Obat <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="nama_obat" name="nama_obat"
                                    placeholder="Contoh: Paracetamol 500mg" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="kategori" class="form-label small fw-bold">Kategori</label>
                                <input type="text" class="form-control form-control-sm" id="kategori" name="kategori"
                                    placeholder="Contoh: Analgesik">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="satuan" class="form-label small fw-bold">Satuan</label>
                                <input type="text" class="form-control form-control-sm" id="satuan" name="satuan"
                                    placeholder="Contoh: Tablet / Botol">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label for="select_kfa" class="form-label small fw-bold">Cari KFA (Kode / Nama
                                    Barang)</label>
                                <select class="form-select form-select-sm" id="select_kfa" style="width: 100%;"></select>
                                <input type="hidden" name="kfa_code" id="kfa_code">
                                <input type="hidden" name="kfa_display" id="kfa_display">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Obat -->
    <div class="modal fade" id="modalEditObat" tabindex="-1" aria-labelledby="modalEditObatLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title fw-bold fs-6" id="modalEditObatLabel">
                        <i class="fas fa-edit me-1"></i> Edit Master Obat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditObat" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_nama_obat" class="form-label small fw-bold">Nama Obat <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="edit_nama_obat"
                                    name="nama_obat" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="edit_kategori" class="form-label small fw-bold">Kategori</label>
                                <input type="text" class="form-control form-control-sm" id="edit_kategori"
                                    name="kategori">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="edit_satuan" class="form-label small fw-bold">Satuan</label>
                                <input type="text" class="form-control form-control-sm" id="edit_satuan"
                                    name="satuan">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_kfa_code" class="form-label small fw-bold">Kode KFA</label>
                                <input type="text" class="form-control form-control-sm" id="edit_kfa_code"
                                    name="kfa_code">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_kfa_display" class="form-label small fw-bold">Display KFA</label>
                                <input type="text" class="form-control form-control-sm" id="edit_kfa_display"
                                    name="kfa_display">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-warning text-white">
                            <i class="fas fa-sync-alt me-1"></i> Update Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Styles Custom untuk Mengecilkan Tabel dan Select2 -->
    <style>
        .select2-container--open {
            z-index: 9999 !important;
        }

        /* Ukuran Font dan Padding Ringkas untuk Tabel */
        .table-custom-sm {
            font-size: 0.85rem;
            /* Ukuran teks lebih kecil */
        }

        .table-custom-sm th {
            padding: 6px 10px !important;
            font-weight: 600;
        }

        .table-custom-sm td {
            padding: 4px 10px !important;
            /* Mengurangi padding vertikal */
        }

        /* Ukuran Tombol Aksi Sangat Ringkas */
        .btn-xs {
            padding: 0.15rem 0.4rem;
            font-size: 0.75rem;
            line-height: 1.2;
            border-radius: 0.2rem;
        }

        /* Penyesuaian DataTables Controls */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.825rem;
            margin-bottom: 8px;
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
            // DataTables
            $('#tableMasterObat').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    emptyTable: "Belum ada data obat.",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Lanjut",
                        previous: "Kembali"
                    }
                }
            });
            // Handler tombol Edit
            $('#tableMasterObat').on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                const kategori = $(this).data('kategori');
                const satuan = $(this).data('satuan');
                const kfacode = $(this).data('kfacode');
                const kfadisplay = $(this).data('kfadisplay');

                $('#formEditObat').attr('action', `/master-obat/${id}`);
                $('#edit_nama_obat').val(nama);
                $('#edit_kategori').val(kategori);
                $('#edit_satuan').val(satuan);
                $('#edit_kfa_code').val(kfacode);
                $('#edit_kfa_display').val(kfadisplay);

                const modal = new bootstrap.Modal(document.getElementById('modalEditObat'));
                modal.show();
            });
            // Inisialisasi Select2 Ajax KFA
            // const $selectKfa = $('#select_kfa').select2({
            //     dropdownParent: $('#modalTambahObat'),
            //     placeholder: 'Ketik nama barang atau kode KFA...',
            //     allowClear: true,
            //     width: '100%',
            //     minimumInputLength: 2,
            //     ajax: {
            //         url: "{{ route('kfa.search') }}",
            //         dataType: 'json',
            //         delay: 250,
            //         data: function(params) {
            //             return {
            //                 q: params.term
            //             };
            //         },
            //         processResults: function(data) {
            //             return {
            //                 results: $.map(data, function(item) {
            //                     return {
            //                         id: item.code,
            //                         text: item.code + ' - ' + item.display,
            //                         code: item.code,
            //                         display: item.display,
            //                     };
            //                 })
            //             };
            //         },
            //         cache: true
            //     }
            // });
            // // Map data terpilih ke input hidden
            // $selectKfa.on('select2:select', function(e) {
            //     var data = e.params.data;
            //     $('#kfa_code').val(data.code);
            //     $('#kfa_display').val(data.display);
            //     $('#satuan').val(data.display);
            // });
            // $selectKfa.on('select2:clear', function() {
            //     $('#kfa_code').val('');
            //     $('#kfa_display').val('');
            // });
            // // Reset saat modal ditutup
            // $('#modalTambahObat').on('hidden.bs.modal', function() {
            //     $selectKfa.val(null).trigger('change');
            //     $('#kfa_code').val('');
            //     $('#kfa_display').val('');
            // });
            const $selectKfa = $('#select_kfa').select2({
                dropdownParent: $('#modalTambahObat'),
                placeholder: 'Ketik nama barang atau kode KFA...',
                allowClear: true,
                width: '100%',
                minimumInputLength: 2,
                ajax: {
                    url: "{{ route('kfa.search') }}",
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
                                    id: item.code,
                                    text: item.code + ' - ' + item.display,
                                    code: item.code,
                                    display: item.display,
                                    satuan: item.satuan, // Pass data satuan
                                    kategori: item.kategori // Pass data kategori
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            // Map data terpilih ke input form
            $selectKfa.on('select2:select', function(e) {
                var data = e.params.data;
                $('#kfa_code').val(data.code);
                $('#kfa_display').val(data.display);
                $('#satuan').val(data.satuan); // Isi input satuan
                $('#kategori').val(data.kategori); // Isi input kategori (sesuaikan ID element)
            });

            // Clear input saat tombol x diklik
            $selectKfa.on('select2:clear', function() {
                $('#kfa_code').val('');
                $('#kfa_display').val('');
                $('#satuan').val('');
                $('#kategori').val('');
            });

            // Reset saat modal ditutup
            $('#modalTambahObat').on('hidden.bs.modal', function() {
                $selectKfa.val(null).trigger('change');
                $('#kfa_code').val('');
                $('#kfa_display').val('');
                $('#satuan').val('');
                $('#kategori').val('');
            });
        });
    </script>
@endsection
