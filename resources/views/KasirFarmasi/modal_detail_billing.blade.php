<!-- Data Ringkasan Pasien -->
<div class="row mb-3">
    <div class="col-md-6">
        <table class="table table-borderless table-sm">
            <tr>
                <td class="fw-bold text-muted" style="width: 130px;">No. RM</td>
                <td>: {{ $kunjungan->pasien->no_rm ?? '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold text-muted">Nama Pasien</td>
                <td>: {{ $kunjungan->pasien->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold text-muted">Poli / Unit</td>
                <td>: {{ $kunjungan->poli['nama_lokasi'] ?? '-' }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table table-borderless table-sm">
            <tr>
                <td class="fw-bold text-muted" style="width: 130px;">No. Kunjungan</td>
                <td>: {{ $kunjungan->no_registrasi ?? '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold text-muted">Tgl. Kunjungan</td>
                <td>: {{ $kunjungan->created_at ? $kunjungan->created_at->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            <tr>
                <td class="fw-bold text-muted">Status Bayar</td>
                <td>:
                    @if (($kunjungan->pembayaran['status'] ?? '') === 'lunas')
                        <span class="badge bg-success">Lunas</span>
                    @else
                        <span class="badge bg-warning text-dark">Belum Bayar</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- Tabel Detail Billing -->
<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;" class="text-center">#</th>
                <th>Nama Item / Layanan</th>
                <th class="text-center" style="width: 100px;">Jumlah</th>
                <th class="text-end" style="width: 150px;">Harga Satuan</th>
                <th class="text-end" style="width: 150px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
           
        </tbody>
        <tfoot class="table-light fw-bold">
            <tr>
                <td colspan="4" class="text-end">Total Tagihan:</td>
                {{-- <td class="text-end text-primary">Rp {{ number_format($total, 0, ',', '.') }}</td> --}}
            </tr>
        </tfoot>
    </table>
</div>
