@extends('Template.Main')
@section('container')
    <div class="container-fluid pt-3">
        <!-- BAR NAVIGASI / HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-file-earmark-text me-2 text-primary"></i>Rincian Billing Pembayaran
                </h4>
                <small class="text-muted">Transaksi Kunjungan #{{ $kunjungan->id }}</small>
            </div>
            <div>
                <a href="{{ route('indexriwayatpembayaran') }}" class="btn btn-sm btn-outline-secondary me-1">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('kasir.cetak-kwitansi', $kunjungan->id) }}" target="_blank" class="btn btn-sm btn-primary">
                    <i class="bi bi-printer me-1"></i> Cetak Kwitansi
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <!-- RINCIAN PASIEN & BILLING HEADER -->
                <div class="row mb-3 border-bottom pb-3">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" style="width: 120px;">No. RM</td>
                                <td class="fw-bold">: {{ $kunjungan->pasien->no_rm ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nama Pasien</td>
                                <td class="fw-bold">:
                                    {{ $kunjungan->pasien->nama_lengkap ?? ($kunjungan->pasien->nama ?? '-') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tgl Kunjungan</td>
                                <td>: {{ \Carbon\Carbon::parse($kunjungan->created_at)->format('d/m/Y H:i') }} WIB</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" style="width: 120px;">Poli / Dokter</td>
                                <td>: {{ $kunjungan->poli->nama_lokasi ?? '-' }} / {{ $kunjungan->nama_dokter ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status Bayar</td>
                                <td>:
                                    @if (strtoupper($kunjungan->pembayaran->status_tagihan ?? '') === 'LUNAS')
                                        <span class="badge bg-success">LUNAS</span>
                                    @else
                                        <span class="badge bg-warning text-dark">BELUM LUNAS</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- TABEL ITEM BILLING -->
                <h6 class="fw-bold mb-2"><i class="bi bi-receipt me-1"></i> Rincian Tagihan</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Deskripsi / Layanan / Obat</th>
                                <th class="text-center" style="width: 80px;">Qty</th>
                                <th class="text-end" style="width: 150px;">Harga Satuan</th>
                                <th class="text-end" style="width: 150px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details as $idx => $row)
                                @php
                                    // Ubah variabel ke numerik secara eksplisit (casting)
                                    $harga = (float) ($row->harga_jual ?? ($row->harga ?? 0));
                                    $qty = (int) ($row->qty ?? 0);
                                    $subtotal = (float) ($row->subtotal ?? $harga * $qty);
                                @endphp
                                <tr>
                                    <td class="text-center"></td>
                                    <td>{{ $row->nama_obat ?? ($row->nama_tindakan ?? 'Layanan Medis') }}</td>
                                    <td class="text-center">{{ $qty }}</td>
                                    <td class="text-end">
                                        Rp {{ number_format($harga, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end">
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-2">Belum ada rincian item tagihan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">Total Tagihan:</td>
                                <td class="text-end text-primary fs-6">
                                    Rp
                                    {{ number_format($kunjungan->pembayaran->total_bayar ?? $details->sum('subtotal'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
