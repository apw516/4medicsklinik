<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Resep - {{ $kunjungan->no_registrasi }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background-color: #fff;
        }

        .receipt-container {
            width: 80mm;
            /* Ukuran Kertas Thermal Struk */
            margin: 0 auto;
            padding: 10px;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .header h5 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .header p {
            margin: 0;
            font-size: 10px;
        }

        .info-table,
        .item-table {
            width: 100%;
            margin-bottom: 8px;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .item-table th {
            border-bottom: 1px dashed #000;
            border-top: 1px dashed #000;
            padding: 4px 0;
            text-align: left;
        }

        .item-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .border-top-dashed {
            border-top: 1px dashed #000;
        }

        .border-bottom-dashed {
            border-bottom: 1px dashed #000;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
        }

        .sign-area {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            text-align: center;
            font-size: 10px;
        }

        /* Rule Khusus Saat Cetak (Print) */
        @media print {
            @page {
                size: 80mm auto;
                /* Menyesuaikan kertas printer thermal */
                margin: 0;
            }

            body {
                background: none;
            }

            .receipt-container {
                width: 100%;
                padding: 5mm;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <!-- Tombol Aksi saat Dilihat di Browser -->
    <div class="no-print text-center py-2 bg-light border-bottom mb-3">
        <button onclick="window.print()" class="btn btn-primary btn-sm me-2">
            <i class="bi bi-printer"></i> Cetak Struk
        </button>
        <button onclick="window.close()" class="btn btn-secondary btn-sm">
            Tutup
        </button>
    </div>

    <div class="receipt-container">
        <!-- Header Klinik / Rumah Sakit -->
        <div class="header">
            <h5>KYNOVAPHARMA</h5>
            <p>Jl. Raya Pabuaran No. 123, Cirebon</p>
            <p>Telp: (0231) 123456 | Farmasi & Apotek</p>
        </div>

        <!-- Judul Resep -->
        <div class="text-center fw-bold mb-2">
            LEMBAR RESEP & BUKTI PENYERAHAN OBAT
        </div>

        <!-- Identitas Pasien & Kunjungan -->
        <table class="info-table">
            <tr>
                <td style="width: 35%;">No. Reg / RM</td>
                <td style="width: 5%;">:</td>
                <td><strong>{{ $kunjungan->no_registrasi }}</strong> / {{ $kunjungan->pasien->no_rm ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama Pasien</td>
                <td>:</td>
                <td><strong>{{ $kunjungan->pasien->nama ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Poli / Unit</td>
                <td>:</td>
                <td>{{ $kunjungan->poli->nama_poli ?? 'Poli Umum' }}</td>
            </tr>
            <tr>
                <td>Tanggal / Jam</td>
                <td>:</td>
                <td>{{ \Carbon\Carbon::parse($kunjungan->created_at)->translatedFormat('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Status Bayar</td>
                <td>:</td>
                <td><strong>LUNAS</strong></td>
            </tr>
        </table>

        <!-- Rincian Obat -->
        <table class="item-table">
            <thead>
                <tr>
                    <th>Item Obat</th>
                    <th style="width: 15%; text-align: center;">Qty</th>
                    <th style="width: 35%; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @forelse($obats as $item)
                    @php
                        $subtotal = $item->subtotal ?? $item->qty * $item->harga;
                        $grandTotal += $subtotal;
                    @endphp
                    <tr>
                        <td colspan="3">
                            <strong>R/ {{ $item->nama_obat ?? $item->nama_layanan }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-left: 10px; font-style: italic;">
                            {{ $item->harga ? 'Rp ' . number_format($item->harga, 0, ',', '.') : '' }}
                        </td>
                        <td style="text-align: center;">{{ $item->qty }} {{ $item->satuan ?? 'pcs' }}</td>
                        <td style="text-align: right;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if (!empty($item->aturan_pakai))
                        <tr>
                            <td colspan="3" style="padding-left: 10px; font-size: 11px;">
                                <em>Signa: {{ $item->aturan_pakai }}</em>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data obat lunas.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="border-top-dashed fw-bold">TOTAL OBAT</td>
                    <td class="border-top-dashed text-end fw-bold">Rp {{ number_format($grandTotal, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Tanda Tangan / Verifikasi -->
        <div class="sign-area">
            <div>
                <p>Pasien / Penerima,</p>
                <br><br>
                <p>( ........................ )</p>
            </div>
            <div>
                <p>Petugas Farmasi,</p>
                <br><br>
                <p>( {{ auth()->user()->name ?? 'Apoteker' }} )</p>
            </div>
        </div>

        <!-- Footer Struk -->
        <div class="footer border-top-dashed pt-2">
            <p class="mb-0">Terima kasih atas kunjungan Anda.</p>
            <p class="mb-0"><em>Semoga Lekas Sembuh</em></p>
            <small style="font-size: 8px;">Cetak: {{ now()->format('d/m/Y H:i:s') }}</small>
        </div>
    </div>

    <!-- Script Otomatis Buka Dialog Print saat Halaman Dimuat -->
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            // Otomatis trigger dialog print setelah dokumen selesai di-load
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>

</body>

</html>
