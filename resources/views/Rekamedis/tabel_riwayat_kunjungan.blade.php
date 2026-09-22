<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover align-middle mb-0" id="tableRiwayat">
        <thead class="table-dark">
            <tr>
                <th style="width: 50px;" class="text-center">No</th>
                <th>Tgl Kunjungan</th>
                <th>No. RM</th>
                <th>Nama Pasien</th>
                <th>Poli / Dokter</th>
                <th class="text-center">Status Pembayaran</th>
                <th class="text-center">Status Kunjungan</th>
                <th style="width: 140px;" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kunjungans as $index =>$item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tgl_kunjungan)->format('d/m/Y H:i') }}</td>
                    <td><span class="badge bg-secondary">{{ $item->no_rm }}</span></td>
                    <td class="fw-bold">{{ $item->nama_lengkap }}</td>
                    <td>
                        <div>{{ $item->nama_poli ?? '-' }}</div>
                        <small class="text-muted">{{ $item->nama_dokter ?? '-' }}</small>
                    </td>
                    <td class="text-center">
                        @if (strtoupper($item->status_pembayaran) === 'LUNAS')
                            <span class="badge bg-success">LUNAS</span>
                        @else
                            <span class="badge bg-warning text-dark">BELUM LUNAS</span>
                        @endif
                    </td>
                    <td class="text-end fw-bold">
                        {{ $item->status_kunjungan }}
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-info text-white"
                            onclick="showDetailKunjungan({{ $item->id }})" title="Detail">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                        <button @if (strtoupper($item->status_pembayaran) === 'LUNAS') disabled @endif type="button" class="btn btn-sm btn-warning text-white"
                            onclick="showEditKunjungan({{ $item->id }})" title="Edit Kunjungan">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button @if (strtoupper($item->status_pembayaran) === 'LUNAS') disabled @endif type="button"
                            class="btn btn-sm btn-danger text-white" onclick="batalkunjungan({{ $item->id }})"
                            title="Batal">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Tidak ada data kunjungan pada rentang tanggal tersebut.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Detail Kunjungan & Billing -->
<div class="modal fade" id="modalDetailKunjungan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-file-earmark-medical me-1"></i> Detail Kunjungan & Billing</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetailBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat detail billing...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Kunjungan -->
<div class="modal fade" id="modalEditKunjungan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditKunjungan">
                @csrf
                <input type="hidden" id="edit_kunjungan_id" name="id">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Edit Data Kunjungan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Pasien</label>
                        <input type="text" class="form-control" id="edit_nama_pasien" readonly disabled>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_tgl_kunjungan" class="form-label fw-bold">Tanggal & Waktu Kunjungan</label>
                            <input type="datetime-local" class="form-control" id="edit_tgl_kunjungan"
                                name="tgl_kunjungan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_poli_id" class="form-label fw-bold">Poli Tujuan</label>
                            <select class="form-select" id="edit_poli_id" name="poli_id" required>
                                <option value="">-- Pilih Poli --</option>
                                @foreach ($unit ?? [] as $poli)
                                    <option value="{{ $poli->id }}">{{ $poli->nama_lokasi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_dokter_id" class="form-label fw-bold">Dokter</label>
                            <select class="form-select" id="edit_dokter_id" name="dokter_id" required>
                                <option value="">-- Pilih Dokter --</option>
                                @foreach ($dokter ?? [] as $dokter)
                                    <option value="{{ $dokter->id }}">{{ $dokter->nama_dokter }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_status_kunjungan" class="form-label fw-bold">Status Kunjungan</label>
                            <select class="form-select" id="edit_status_kunjungan" name="status_kunjungan" required>
                                <option value="ANTRIAN">ANTRI</option>
                                <option value="SELESAI">SELESAI</option>
                                <option value="BATAL">BATAL</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Keluhan Utama</label>
                            <input type="text" class="form-control" name="keluhan_utama" id="edit_keluhan_utama">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#tableRiwayat')) {
            $('#tableRiwayat').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                },
                "pageLength": 10,
                "ordering": true
            });
        }

        // Submit Form Edit via AJAX
        $('#formEditKunjungan').on('submit', function(e) {
            e.preventDefault();
            let id = $('#edit_kunjungan_id').val();

            $.ajax({
                url: "{{ url('/pasien/update-kunjungan') }}/" + id,
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    $('#modalEditKunjungan').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat memperbarui data.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: errorMessage
                    });
                }
            });
        });
    });

    function showDetailKunjungan(kunjunganId) {
        $('#modalDetailKunjungan').modal('show');
        $('#modalDetailBody').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Memuat detail billing...</p>
            </div>
        `);
        $.ajax({
            url: "{{ url('/pasien/detail-kunjungan') }}/" + kunjunganId,
            type: "GET",
            success: function(response) {
                $('#modalDetailBody').html(response);
            },
            error: function() {
                $('#modalDetailBody').html(`
                    <div class="alert alert-danger text-center mb-0">
                        Gagal mengambil detail kunjungan. Silakan coba lagi.
                    </div>
                `);
            }
        });
    }

    function showEditKunjungan(kunjunganId) {
        $.ajax({
            url: "{{ url('/pasien/get-kunjungan') }}/" + kunjunganId,
            type: "GET",
            success: function(data) {
                $('#edit_kunjungan_id').val(data.id);
                $('#edit_nama_pasien').val(data.nama_lengkap);

                // Format tanggal ke YYYY-MM-DDTHH:MM untuk input datetime-local
                if (data.tgl_masuk) {
                    let date = new Date(data.tgl_masuk);
                    let formattedDate = date.toISOString().slice(0, 16);
                    $('#edit_tgl_kunjungan').val(formattedDate);
                }

                $('#edit_poli_id').val(data.poli_id);
                $('#edit_dokter_id').val(data.dokter_id);
                $('#edit_status_kunjungan').val(data.status_kunjungan);
                $('#edit_keluhan_utama').val(data.keluhan_utama);
                $('#modalEditKunjungan').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Tidak dapat mengambil data kunjungan.'
                });
            }
        });
    }

    function batalkunjungan(kunjunganId) {
        Swal.fire({
            title: "Anda yakin?",
            text: "Data kunjungan pasien akan dibatalkan...",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, batal!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/pasien/batalkunjungan') }}/" + kunjunganId,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan sistem.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: errorMessage
                        });
                    }
                });
            }
        });
    }
</script>
