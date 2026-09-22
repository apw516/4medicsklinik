
<div class="row mb-3 border-bottom pb-3">
    <div class="col-md-6">
        <table class="table table-sm table-borderless mb-0">
            <tr>
                <td class="text-muted" style="width: 120px;">No. RM</td>
                <td class="fw-bold">: {{ $kunjungan->pasien->no_rm }}</td>
            </tr>
            <tr>
                <td class="text-muted">Nama Pasien</td>
                <td class="fw-bold">: {{ $kunjungan->pasien->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="text-muted">Tgl Kunjungan</td>
                <td>: {{ \Carbon\Carbon::parse($kunjungan->created_at)->format('d/m/Y H:i') }}</td>
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

<!-- SECTION BILLING -->
<h6 class="fw-bold mb-2"><i class="bi bi-receipt me-1"></i> Rincian Billing</h6>
<div class="table-responsive mb-4">
    <table class="table table-sm table-bordered mb-0">
        <thead class="table-light">
            <tr>
                <th class="text-center" style="width: 50px;">No</th>
                <th>Deskripsi / Layanan</th>
                <th class="text-center" style="width: 80px;">Qty</th>
                <th class="text-end" style="width: 150px;">Harga Satuan</th>
                <th class="text-end" style="width: 150px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @forelse($details as $idx => $row)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ mb_substr($row->nama_obat, 0, 3) }} {{ $row->nama_tindakan }}</td>
                    <td class="text-center">{{ $row->qty }}</td>
                    <td class="text-end">Rp
                        @if ($row->harga_jual != '')
                            {{ number_format($row->harga_jual, 0, ',', '.') }}
                            @else{{ number_format($row->harga, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-end">Rp {{ number_format($row->subtotal, 0, ',', '.') }}</td>
                </tr>
                @php
                    $total = $row->subtotal + $total;
                @endphp
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Belum ada item tagihan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="table-light fw-bold">
            <tr>
                <td colspan="4" class="text-end">Total Tagihan:</td>
                <td class="text-end text-primary">
                    Rp {{ number_format($total ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<hr class="my-4">

<!-- SECTION VIEW-ONLY ELEKTRONIK REKAM MEDIS (ERM) -->
<div class="card border">
    <div class="card-header bg-light fw-bold text-dark">
        <i class="bi bi-file-earmark-medical me-1"></i> Ringkasan Rekam Medis (ERM)
    </div>
    <div class="card-body">

        <!-- 1. ANAMNESIS (SUBJEKTIF) -->
        <div class="mb-4">
            <h6 class="text-dark fw-bold border-bottom pb-2">
                <i class="bi bi-person-lines-fill me-1"></i> 1. Anamnesis (Subjektif)
            </h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-1">Keluhan Utama</label>
                    <div class="p-2 bg-light rounded border text-dark">
                        {{ !empty($erm->keluhan_utama) ? $erm->keluhan_utama : $kunjungan->keluhan_utama ?? ($kunjungan->keluhan ?? '-') }}
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-1">Riwayat Penyakit Dahulu / Keluarga</label>
                    <div class="p-2 bg-light rounded border text-dark">
                        {{ $erm->riwayat_penyakit ?? '-' }}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="p-3 bg-light rounded border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0 text-dark small">
                                <i class="bi bi-exclamation-octagon text-danger me-1"></i> Riwayat Alergi (SATUSEHAT
                                AllergyIntolerance)
                            </label>
                            @if (isset($erm) && !empty($erm->satusehat_allergy_id))
                                <span class="badge bg-success font-monospace" style="font-size: 0.7rem;">
                                    <i class="bi bi-check-circle me-1"></i> Bridged (ID:
                                    {{ $erm->satusehat_allergy_id }})
                                </span>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <span class="text-muted small">Kategori:</span>
                                <strong>{{ ucfirst($erm->kategori_alergi ?? 'Tidak Ada') }}</strong>
                            </div>
                            <div class="col-md-8">
                                <span class="text-muted small">Detail Bahan/Obat:</span>
                                <strong>{{ $erm->detail_alergi ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. PEMERIKSAAN FISIK & TTV (OBJEKTIF) -->
        <div class="mb-4">
            <h6 class="text-dark fw-bold border-bottom pb-2">
                <i class="bi bi-activity me-1"></i> 2. Tanda-Tanda Vital & Pemeriksaan Fisik (Objektif)
            </h6>
            <div class="row g-2 text-center mb-3">
                <div class="col-md-2 col-4">
                    <div class="p-2 border rounded bg-light">
                        <small class="text-muted d-block">Tekanan Darah</small>
                        <span class="fw-bold">{{ $erm->td_sistole ?? '-' }}/{{ $erm->td_diastole ?? '-' }}</span>
                        <small class="text-muted">mmHg</small>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-2 border rounded bg-light">
                        <small class="text-muted d-block">Nadi</small>
                        <span class="fw-bold">{{ $erm->nadi ?? '-' }}</span> <small class="text-muted">x/mnt</small>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-2 border rounded bg-light">
                        <small class="text-muted d-block">Suhu</small>
                        <span class="fw-bold">{{ $erm->suhu ?? '-' }}</span> <small class="text-muted">°C</small>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-2 border rounded bg-light">
                        <small class="text-muted d-block">SpO2</small>
                        <span class="fw-bold">{{ $erm->spo2 ?? '-' }}</span> <small class="text-muted">%</small>
                    </div>
                </div>
                <div class="col-md-2 col-4">
                    <div class="p-2 border rounded bg-light">
                        <small class="text-muted d-block">Laju Napas</small>
                        <span class="fw-bold">{{ $erm->respirasi ?? '-' }}</span> <small
                            class="text-muted">x/mnt</small>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <label class="form-label text-muted small mb-1">Hasil Pemeriksaan Fisik / Tambahan</label>
                <div class="p-2 bg-light rounded border text-dark">
                    {{ $erm->pemeriksaan_fisik ?? '-' }}
                </div>
            </div>
        </div>

        <!-- 3. DIAGNOSIS / ICD-10 (ASESMEN) -->
        <div class="mb-4">
            <h6 class="text-dark fw-bold border-bottom pb-2">
                <i class="bi bi-file-earmark-medical me-1"></i> 3. Diagnosis / Asesmen (ICD-10)
            </h6>
            <div class="row g-2">
                <div class="col-md-12 mb-2">
                    <label class="form-label text-muted small mb-1">Diagnosa Utama (ICD-10)</label>
                    <div class="p-2 bg-light rounded border fw-bold text-dark">
                        @if (!empty($erm->icd10_code))
                            <span class="badge bg-primary me-1">{{ $erm->icd10_code }}</span>
                            {{ $erm->icd10_display }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <label class="form-label text-muted small mb-1">Catatan / Diagnosa Sekunder</label>
                    <div class="p-2 bg-light rounded border text-dark">
                        {{ $erm->diagnosa_catatan ?? '-' }}
                    </div>
                </div>
            </div>
        </div>

   

        <!-- 5. PENATALAKSANAAN & RESEP OBAT (PLAN) -->
        <div>
            <h6 class="text-dark fw-bold border-bottom pb-2">
                <i class="bi bi-capsule me-1"></i> 5. Penatalaksanaan & Resep Obat (Plan)
            </h6>
            <div class="mb-3">
                <label class="form-label text-muted small mb-1">Rencana Tindakan / Edukasi</label>
                <div class="p-2 bg-light rounded border text-dark">
                    {{ $erm->tindakan_edukasi ?? '-' }}
                </div>
            </div>

        </div>

    </div>
</div>
