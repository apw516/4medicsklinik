<!-- Header Informasi Transaksi -->
<div class="card mb-3 border-0 shadow-sm">
    <div class="card-body bg-light rounded p-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.75rem;">
                    Nomor Transaksi
                </small>
                <h5 class="fw-bold text-primary mb-0">
                    <i class="fas fa-receipt me-1"></i>
                    {{ $billingDetails->first()->no_transaksi ?? '-' }}
                </h5>
            </div>
            <div class="col-md-6 text-md-end mt-2 mt-md-0 d-flex align-items-center justify-content-md-end gap-2">
                @php
                    $statusUtama = $billingDetails->first()->sttsheader ?? 'BELUM BAYAR';
                    $isLunas = strtoupper($statusUtama) === 'LUNAS';
                    $pembayaranId = $billingDetails->first()->pembayaran_id ?? null;
                @endphp

                <span class="badge {{ $isLunas ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2 fs-6">
                    <i class="fas {{ $isLunas ? 'fa-check-circle' : 'fa-clock' }} me-1"></i>
                    {{ strtoupper($statusUtama) }}
                </span>

                {{-- @if ($pembayaranId)
                    <a href="{{ route('cetak-pembayaran', $pembayaranId) }}" target="_blank"
                        class="btn btn-outline-primary btn-sm px-3">
                        <i class="fas fa-print me-1"></i> Cetak
                    </a>
                @endif --}}
            </div>
        </div>
    </div>
</div>

<!-- Tabel Rincian Detail Billing -->
<div class="table-responsive">
    <table class="table table-hover table-striped align-middle border mb-0" style="font-size: 0.875rem;">
        <thead class="table-light">
            <tr>
                <th style="width: 40px;" class="text-center">#</th>
                <th>Deskripsi Item / Layanan</th>
                <th style="width: 110px;" class="text-center">Jenis Item</th>
                <th style="width: 70px;" class="text-center">Qty</th>
                <th style="width: 130px;" class="text-end">Harga Satuan</th>
                <th style="width: 140px;" class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $totalTagihan = 0; @endphp

            @forelse($billingDetails as $index => $item)
                @php
                    $namaDeskripsi = $item->nama_tindakan ?? ($item->nama_obat ?? 'Item Tanpa Nama');
                    $qty = (float) ($item->qty ?? 0);
                    $harga = (float) ($item->harga ?? 0);
                    $subtotal = (float) ($item->subtotal ?? $qty * $harga);

                    $totalTagihan += $subtotal;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong class="text-dark">{{ $namaDeskripsi }}</strong>
                    </td>
                    <td class="text-center">
                        @if (strtolower($item->jenis_item ?? '') === 'obat')
                            <span class="badge bg-info text-dark">
                                <i class="fas fa-pills me-1"></i>Obat
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="fas fa-user-md me-1"></i>Tindakan
                            </span>
                        @endif
                    </td>
                    <td class="text-center fw-semibold">{{ $qty }}</td>
                    <td class="text-end">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold text-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                        Tidak ada rincian item pembayaran untuk transaksi ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="table-light">
            <tr>
                <td colspan="5" class="text-end fw-bold fs-6">Total Pembayaran:</td>
                <td class="text-end fw-bold text-primary fs-6">
                    Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>
