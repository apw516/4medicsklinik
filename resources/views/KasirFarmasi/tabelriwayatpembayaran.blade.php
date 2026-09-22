<div class="table-responsive">
    <table id="tableRiwayatPembayaran" class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;" class="text-center">#</th>
                <th>No. Transaksi</th>
                <th>Pasien</th>
                <th>Poli / Lokasi</th>
                <th>Tgl. Masuk</th>
                <th class="text-end">Jumlah Bayar</th>
                <th class="text-center">Metode</th>
                <th class="text-center">Status</th>
                <th style="width: 150px;" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembayarans as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong class="text-primary">{{ $item->no_transaksi ?? '-' }}</strong><br>
                        <small class="text-muted">SK: {{ $item->no_surat_kontrol ?? '-' }}</small>
                    </td>
                    <td>
                        <strong>{{ $item->nama_lengkap }}</strong><br>
                        <small class="text-muted">RM: {{ $item->no_rm }}</small>
                    </td>
                    <td>{{ $item->nama_lokasi }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tgl_masuk)->format('d/m/Y H:i') }}</td>
                    <td class="text-end fw-bold">
                        Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ strtoupper($item->metode_pembayaran ?? 'CASH') }}</span>
                    </td>
                    <td class="text-center">
                        @if (strtoupper($item->status_tagihan) === 'LUNAS')
                            <span class="badge bg-success">Lunas</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ $item->status_tagihan }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-info btn-sm text-white"
                                onclick="openDetailModal({{ $item->id_pembayaran }})" title="Lihat Detail">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle fa-2x mb-2"></i><br>
                        Tidak ada riwayat pembayaran pada rentang tanggal tersebut.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Detail Pembayaran -->
<div class="modal fade" id="modalDetailBilling" tabindex="-1" aria-labelledby="modalDetailBillingLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetailBillingLabel">
                    <i class="fas fa-file-invoice-dollar me-2"></i>Detail Billing Pembayaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetailContent">
                <!-- Data AJAX dimasukkan ke sini -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data detail...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="btnCetakModal" target="_blank" class="btn btn-primary">
                    <i class="fas fa-print me-1"></i> Cetak Nota
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Script DataTables & AJAX Modal -->
<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        if (!$.fn.DataTable.isDataTable('#tableRiwayatPembayaran')) {
            $('#tableRiwayatPembayaran').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                },
                "pageLength": 10,
                "ordering": true
            });
        }
    });

    function openDetailModal(pembayaranId) {
        // Tampilkan modal terlebih dahulu
        let modalElement = document.getElementById('modalDetailBilling');
        let myModal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);

        // Reset isi modal ke tampilan loading
        $('#modalDetailContent').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat data detail...</p>
        </div>
    `);

        // Dynamic URL menggunakan route Blade + Replace Placeholder
        let urlCetak = "{{ route('cetak-pembayaran', ':id') }}".replace(':id', pembayaranId);
        let urlDetail = "{{ route('detail-pembayaran', ':id') }}".replace(':id', pembayaranId);

        // Set URL Cetak pada tombol modal
        $('#btnCetakModal').attr('href', urlCetak);

        myModal.show();

        // Request data via AJAX
        $.ajax({
            url: urlDetail,
            type: 'GET',
            success: function(response) {
                $('#modalDetailContent').html(response);
            },
            error: function(xhr) {
                $('#modalDetailContent').html(`
                <div class="alert alert-danger mb-0">
                    Gagal mengambil data detail. Silakan coba lagi.
                </div>
            `);
            }
        });
    }
</script>
