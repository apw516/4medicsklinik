<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembayaran - {{ $header->no_transaksi ?? '-' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            color: #000;
            background-color: #f8f9fa;
        }

        .nota-wrapper {
            max-width: 450px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border: 1px dashed #000;
        }

        .border-dashed {
            border-bottom: 1px dashed #000 !important;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background-color: #fff;
            }

            .nota-wrapper {
                border: none;
                padding: 0;
                margin: 0 auto;
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- Tombol Aksi (Tidak Ikut Tercetak) -->
    <div class="container my-3 no-print text-center">
        <button onclick="window.print()" class="btn btn-primary btn-sm me-2">
            <i class="fas fa-print"></i> Cetak Struk
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm">
            Tutup
        </button>
    </div>

    <div class="nota-wrapper">
        <!-- Header Klinik / Rumah Sakit -->
        <div class="text-center mb-2">
            <h5 class="fw-bold m-0">{{ $mt_client->nama_klinik }}</h5>
            <small class="d-block">{{ $mt_client->alamat }}</small>
            <small class="d-block">Telp: {{ $mt_client->telp }}</small>
            <div class="border-dashed my-2"></div>
            <h6 class="fw-bold text-uppercase m-0">BUKTI PEMBAYARAN</h6>
        </div>

        <!-- Informasi Transaksi -->
        <table class="w-100 mb-2" style="line-height: 1.4;">
            <tr>
                <td style="width: 35%;">No. Transaksi</td>
                <td>: <strong>{{ $header->no_transaksi ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ \Carbon\Carbon::parse($header->tgl_transaksi ?? now())->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Metode Bayar</td>
                <td>: {{ strtoupper($header->metode_pembayaran ?? 'CASH') }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>: <strong>{{ strtoupper($header->status_tagihan ?? 'LUNAS') }}</strong></td>
            </tr>
        </table>

        <div class="border-dashed my-2"></div>

        <!-- Tabel Ringkasan Tagihan -->
        <table class="w-100 mb-2">
            <thead>
                <tr class="border-dashed">
                    <th class="text-start pb-1">Deskripsi</th>
                    <th class="text-end pb-1" style="width: 120px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = $billingDetails->sum(function ($item) {
                        $qty = (float) ($item->qty ?? 0);
                        $harga = (float) ($item->harga ?? 0);
                        return (float) ($item->subtotal ?? $qty * $harga);
                    });
                @endphp
                <tr>
                    <td class="pt-2 fw-bold">Total Rincian Pelayanan & Obat</td>
                    <td class="pt-2 text-end align-top fw-bold">Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="border-dashed my-2"></div>

        <!-- Total Pembayaran -->
        <table class="w-100 fw-bold fs-6">
            <tr>
                <td>TOTAL BAYAR</td>
                <td class="text-end">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="border-dashed my-2"></div>

        <!-- Footer Struk -->
        <div class="text-center mt-3">
            <small class="d-block">Terima Kasih atas Kunjungan Anda</small>
            <small class="d-block">Semoga Lekas Sembuh</small>
        </div>
    </div>

    <script>
        // Otomatis munculkan dialog print setelah halaman selesai dimuat
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>
