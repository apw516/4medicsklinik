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
            <h5 class="fw-bold m-0">{{ $mt_client->nama_klinik}}</h5>
            <small class="d-block">{{ $mt_client->alamat}}</small>
            <small class="d-block">Telp: {{ $mt_client->telp}}</small>
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

        <!-- Tabel Detail Item -->
        <table class="w-100 mb-2">
            <thead>
                <tr class="border-dashed">
                    <th class="text-start pb-1">Item</th>
                    <th class="text-center pb-1" style="width: 40px;">Qty</th>
                    <th class="text-end pb-1" style="width: 80px;">Harga</th>
                    <th class="text-end pb-1" style="width: 90px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach ($billingDetails as $item)
                    @php
                        $rawNama = $item->nama_tindakan ?? ($item->nama_obat ?? 'Item Tanpa Nama');
                        $jenisItem = strtolower($item->jenis_item ?? '');

                        // Logika Sensor Obat (Menyisakan awal dan tengah, sisanya disensor '*')
                        if ($jenisItem === 'obat' && !empty($rawNama)) {
                            $words = explode(' ', trim($rawNama));
                            $count = count($words);

                            if ($count === 1) {
                                // 1 Kata: Tampilkan 3 karakter awal, sisanya '*'
                                $len = strlen($words[0]);
                                $namaItem = $len > 3 
                                    ? substr($words[0], 0, 3) . str_repeat('*', $len - 3) 
                                    : $words[0];
                            } elseif ($count === 2) {
                                // 2 Kata: Kata ke-1 tampil utuh, Kata ke-2 disensor
                                $namaItem = $words[0] . ' ' . str_repeat('*', strlen($words[1]));
                            } else {
                                // 3 Kata atau Lebih: Kata ke-1 dan tengah/ke-2 tampil, sisanya '*'
                                $middleIndex = (int) floor($count / 2);
                                $maskedWords = [];

                                foreach ($words as $idx => $word) {
                                    if ($idx === 0 || $idx === $middleIndex) {
                                        $maskedWords[] = $word;
                                    } else {
                                        $maskedWords[] = str_repeat('*', strlen($word));
                                    }
                                }
                                $namaItem = implode(' ', $maskedWords);
                            }
                        } else {
                            $namaItem = $rawNama;
                        }

                        $qty = (float) ($item->qty ?? 0);
                        $harga = (float) ($item->harga ?? 0);
                        $subtotal = (float) ($item->subtotal ?? $qty * $harga);
                        $total += $subtotal;
                    @endphp
                    <tr>
                        <td colspan="4" class="pt-2 fw-bold">{{ $namaItem }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-2"><small>({{ ucfirst($item->jenis_item ?? 'item') }})</small></td>
                        <td class="text-center align-top">{{ $qty }}</td>
                        <td class="text-end align-top">{{ number_format($harga, 0, ',', '.') }}</td>
                        <td class="text-end align-top">{{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
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