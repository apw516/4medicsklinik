<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi_Pembayaran_{{ $kunjungan->pasien->no_rm ?? 'Klinik' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .kwitansi-box {
            max-width: 750px;
            margin: auto;
            border: 1px solid #000;
            padding: 20px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
            .kwitansi-box { border: none; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print text-center my-3">
        <button onclick="window.print()" class="btn btn-primary btn-sm me-2">Cetak Kwitansi</button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm">Tutup</button>
    </div>

    <div class="kwitansi-box mt-3">
        <!-- HEADER KINIK / RS -->
        <div class="row border-bottom pb-2 mb-3 align-items-center">
            <div class="col-8">
                <h4 class="fw-bold mb-0">KLINIK / RUMAH SAKIT</h4>
                <small class="d-block">Jl. Raya Utama No. 123, Cirebon - Jawa Barat</small>
                <small>Telp: (0231) 123456 | Email: info@klinik.com</small>
            </div>
            <div class="col-4 text-end">
                <h5 class="fw-bold text-uppercase border p-2 text-center d-inline-block">KWITANSI</h5>
            </div>
        </div>

        <!-- INFORMASI TRANSAKSI -->
        <div class="row mb-3">
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td style="width: 110px;">No. Transaksi</td>
                        <td>: <strong>#KW-{{ str_pad($kunjungan->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                    </tr>
                    <tr>
                        <td>No. RM</td>
                        <td>: {{ $kunjungan->pasien->no_rm ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Nama Pasien</td>
                        <td>: {{ $kunjungan->pasien->nama_lengkap ?? $kunjungan->pasien->nama ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td style="width: 110px;">Tanggal Transaksi</td>
                        <td>: {{ \Carbon\Carbon::parse($kunjungan->updated_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Poli / Klinik</td>
                        <td>: {{ $kunjungan->poli->nama_lokasi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Dokter</td>
                        <td>: {{ $kunjungan->nama_dokter ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- RINCIAN TABLE -->
        <table class="table table-sm table-bordered mb-3">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 40px;">No</th>
                    <th>Uraian / Detail Layanan & Obat</th>
                    <th style="width: 60px;">Qty</th>
                    <th style="width: 120px;">Harga Satuan</th>
                    <th style="width: 130px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kunjungan->billing_details as $idx => $row)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $row->nama_obat ?? $row->nama_tindakan ?? 'Layanan Medis' }}</td>
                        <td class="text-center">{{ $row->qty }}</td>
                        <td class="text-end">Rp {{ number_format($row->harga_jual ?? $row->harga ?? 0, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($row->subtotal ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada rincian item.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">TOTAL BAYAR:</th>
                    <th class="text-end">
                        Rp {{ number_format($kunjungan->pembayaran->total_bayar ?? $kunjungan->billing_details->sum('subtotal'), 0, ',', '.') }}
                    </th>
                </tr>
            </tfoot>
        </table>

        <!-- FOOTER TANDA TANGAN -->
        <div class="row pt-4 align-items-end">
            <div class="col-8">
                <small class="fst-italic">* Pembayaran yang sudah dilakukan tidak dapat ditarik kembali.</small><br>
                <small class="fw-bold">Status: LUNAS</small>
            </div>
            <div class="col-4 text-center">
                <small>Kasir / Petugas,</small>
                <br><br><br>
                <strong>( {{ auth()->user()->name ?? 'Kasir' }} )</strong>
            </div>
        </div>
    </div>

</body>
</html>