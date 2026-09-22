<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-primary">
            <i class="bi bi-table me-1"></i> Hasil Pencarian Pasien
        </h6>
        <span class="badge bg-primary rounded-pill px-3 py-2">
            Total: {{ number_format($pasiens->total()) }} Pasien
        </span>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table id="tabelpasien" class="table table-hover align-middle mb-0 w-100" style="font-size: 0.88rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 5%;">No</th>
                        <th style="width: 12%;">No. RM</th>
                        <th style="width: 22%;">Identitas Pasien</th>
                        <th style="width: 15%;">TTL / Umur</th>
                        <th style="width: 22%;">Alamat & Kontak</th>
                        <th style="width: 12%;">SATUSEHAT</th>
                        <th class="text-center" style="width: 12%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pasiens as $index => $pasien)
                        <tr>
                            <td class="ps-3 fw-bold text-muted">
                                {{ $pasiens->firstItem() + $index }}
                            </td>
                            <td>
                                <span
                                    class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold fs-6">
                                    {{ $pasien->no_rm }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">
                                    {{ $pasien->gelar_depan }} {{ $pasien->nama_lengkap }} {{ $pasien->gelar_belakang }}
                                    @if ($pasien->jenis_kelamin == 'L')
                                        <span class="badge bg-info text-dark ms-1">L</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger ms-1">P</span>
                                    @endif
                                </div>
                                <small class="text-muted d-block">
                                    <i class="bi bi-card-text me-1"></i>NIK: {{ $pasien->nik }}
                                </small>
                            </td>
                            <td>
                                <div>
                                    {{ $pasien->tempat_lahir }},
                                    {{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->translatedFormat('d M Y') }}
                                </div>
                                <small class="text-muted">
                                    ({{ \Carbon\Carbon::parse($pasien->tanggal_lahir)->age }} Thn)
                                </small>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 220px;" title="{{ $pasien->alamat }}">
                                    {{ $pasien->alamat }}
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-whatsapp text-success me-1"></i>{{ $pasien->no_hp ?? '-' }}
                                </small>
                            </td>
                            <td>
                                @if ($pasien->ihs_number)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle-fill me-1"></i>IHS Ready
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        IHS (-)
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-warning btn-edit-pasien"
                                        data-id="{{ $pasien->id }}" data-rm="{{ $pasien->no_rm }}"
                                        data-nik="{{ $pasien->nik }}" data-nama="{{ $pasien->nama_lengkap }}"
                                        data-gelar-depan="{{ $pasien->gelar_depan }}"
                                        data-gelar-belakang="{{ $pasien->gelar_belakang }}"
                                        data-jk="{{ $pasien->jenis_kelamin }}"
                                        data-tmp-lahir="{{ $pasien->tempat_lahir }}"
                                        data-tgl-lahir="{{ $pasien->tanggal_lahir }}"
                                        data-alamat="{{ $pasien->alamat }}" data-hp="{{ $pasien->no_hp }}"
                                        data-ihs="{{ $pasien->ihs_number }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a rm="{{ $pasien->id }}" nama="{{ $pasien->nama_lengkap }}"
                                        class="btn btn-outline-danger hapuspasien" title="Hapus pasien ...">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <a rm="{{ $pasien->no_rm }}" class="btn btn-outline-success pilihpasien"
                                        title="Daftar Pelayanan">
                                        <i class="bi bi-arrow-right-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <h6 class="text-muted fw-bold">Data Pasien Tidak Ditemukan</h6>
                                <p class="text-muted small mb-0">Coba ubah kata kunci pencarian Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit Pasien -->
<div class="modal fade" id="modalEditPasien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Pasien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditPasien">
                @csrf
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="v_editpasien"></div>
                    {{-- <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">No. RM</label>
                            <input type="text" id="edit_no_rm" class="form-control" readonly>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nik" name="nik" class="form-control" maxlength="16"
                                required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Gelar Depan</label>
                            <input type="text" id="edit_gelar_depan" name="gelar_depan" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nama_lengkap" name="nama_lengkap" class="form-control"
                                required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Gelar Belakang</label>
                            <input type="text" id="edit_gelar_belakang" name="gelar_belakang"
                                class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Jenis Kelamin</label>
                            <select id="edit_jenis_kelamin" name="jenis_kelamin" class="form-control" required>
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" id="edit_tempat_lahir" name="tempat_lahir" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tanggal Lahir</label>
                            <input type="date" id="edit_tanggal_lahir" name="tanggal_lahir" class="form-control"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. HP / WA</label>
                            <input type="text" id="edit_no_hp" name="no_hp" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">IHS Number (SATUSEHAT)</label>
                            <input type="text" id="edit_ihs_number" class="form-control" readonly
                                placeholder="Otomatis terisi jika sync berhasil">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea id="edit_alamat" name="alamat" class="form-control" rows="2"></textarea>
                        </div>
                    </div> --}}
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold" id="btnSimpanEdit">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        var tablePasien = $('#tabelpasien').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 10,
            columnDefs: [{
                    orderable: false,
                    targets: [0, 6]
                } // Nonaktifkan sorting untuk Kolom No & Aksi
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari pasien...",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Data pasien tidak ditemukan",
                paginate: {
                    first: "<i class='bi bi-chevron-double-left'></i>",
                    last: "<i class='bi bi-chevron-double-right'></i>",
                    next: "<i class='bi bi-chevron-right'></i>",
                    previous: "<i class='bi bi-chevron-left'></i>"
                }
            }
        });

        // Handler Klik Pilih Pasien (Event Delegation agar tetap bekerja setelah pagination DataTables)
        $(document).on('click', '.pilihpasien', function(event) {
            let rm = $(this).attr('rm');
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    rm: rm
                },
                url: "<?= route('ambil_form_pendaftaran') ?>",
                error: function(response) {
                    Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
                },
                success: function(response) {
                    $('.v_1').attr('hidden', true);
                    $('.v_2').removeAttr('hidden');
                    $('.v_formnya').html(response);
                }
            });
        });
        $(document).on('click', '.hapuspasien', function(event) {
            let nama = $(this).attr('nama');
            let rm = $(this).attr('rm');
            Swal.fire({
                title: "Anda yakin ?",
                text: "Data pasien" + nama + " Akan dihapus ....!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus pasien !"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('pasien.hapus') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            rm: rm,
                        },
                        success: function(response) {
                            Swal.fire({
                                position: "top-end",
                                icon: "success",
                                title: "Data pasien berhasil dihapus ...",
                                showConfirmButton: false,
                                timer: 1500
                            });
                            location.reload()
                        },
                        error: function(xhr) {
                            let errorMsg = "Terjadi kesalahan.";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            alert(errorMsg);
                        }
                    });
                }
            });
        });

        // Open Modal Edit & Load Data
        $(document).on('click', '.btn-edit-pasien', function() {
            let btn = $(this);
            id = btn.data('id');
            spinner.show();

            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id

                },
                url: '<?= route('ambil_form_editpasien') ?>',
                success: function(response) {
                    spinner.hide();
                    $('.v_editpasien').html(response);
                    $('#modalEditPasien').modal('show');
                }
            });
        });

        // Submit Form Edit Pasien
        $('#formEditPasien').on('submit', function(e) {
            e.preventDefault();
            let btnSubmit = $('#btnSimpanEdit');
            btnSubmit.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan & Cek SATUSEHAT...'
            );
            $.ajax({
                url: "{{ route('pasien.update') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    btnSubmit.prop('disabled', false).html(
                        '<i class="bi bi-save me-1"></i> Simpan Perubahan'
                    );

                    if (response.success) {
                        $('#modalEditPasien').modal('hide');
                        let message = response.message;
                        if (response.ihs_number) {
                            message += '<br><b>IHS SATUSEHAT: ' + response.ihs_number +
                                '</b>';
                        }

                        Swal.fire({
                            title: "Berhasil!",
                            html: message,
                            icon: "success"
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    btnSubmit.prop('disabled', false).html(
                        '<i class="bi bi-save me-1"></i> Simpan Perubahan'
                    );
                    let res = xhr.responseJSON;
                    Swal.fire({
                        title: "Gagal!",
                        text: res?.message || "Terjadi kesalahan sistem.",
                        icon: "error"
                    });
                }
            });
        });
    });
</script>
