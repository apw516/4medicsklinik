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
                {{-- <th class="text-end">Total Biaya</th> --}}
                <th style="width: 100px;" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kunjungans as $index => $item)
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
                            onclick="showDetailKunjungan({{ $item->id }})">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                        <button @if (strtoupper($item->status_pembayaran) === 'LUNAS') disabled @endif type="button"
                            class="btn btn-sm btn-danger text-white" onclick="batalkunjungan({{ $item->id }})">
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

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        if (!$.fn.DataTable.isDataTable('#tableRiwayatPembayaran')) {
            $('#tableRiwayat').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                },
                "pageLength": 10,
                "ordering": true
            });
        }
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
                    type: "GET", // Disarankan ganti ke POST/PUT jika mengubah data
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        }).then(() => {
                            location.reload(); // Reload halaman jika diperlukan
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
