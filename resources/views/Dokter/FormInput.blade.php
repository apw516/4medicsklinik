<div class="card card-outline card-primary shadow-sm mt-2">
    <div class="card-header d-flex justify-content-between align-items-center bg-light">
        <h4 class="card-title mb-0 text-primary">
            <i class="bi bi-journal-medical me-2"></i> Form Rekam Medis Elektronik (ERM) Klinik
        </h4>
        <button type="button" class="btn btn-danger btn-sm" onclick="kembali()">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Tabel
        </button>
    </div>
    <!-- Ringkasan Identitas Pasien & Status SATUSEHAT -->
    {{-- <div class="card-body bg-light border-bottom">
        <div class="row g-2 align-items-center">
            <div class="col-md-2">
                <small class="text-muted d-block">No. Rekam Medis</small>
                <span class="fw-bold font-monospace fs-6 text-dark">{{ $pasien->no_rm }}</span>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Nama Lengkap Pasien</small>
                <span class="fw-bold text-dark">{{ $pasien->nama_lengkap ?? $pasien->nama }}</span>
                ({{ $pasien->jenis_kelamin }})
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">NIK / IHS SATUSEHAT</small>
                <span class="fw-bold text-dark">{{ $pasien->nik }}</span> /
                <span class="badge bg-info text-dark">{{ $pasien->ihs_number ?? 'Belum Bridging' }}</span>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block">Status Encounter</small>
                @if (isset($kunjungan) && !empty($kunjungan->satusehat_encounter_id))
                    <span class="badge bg-success font-monospace" style="font-size: 0.75rem;">
                        <i class="bi bi-check-circle me-1"></i> Terkirim
                    </span>
                    <div class="text-muted font-monospace lh-1 mt-1" style="font-size: 0.7rem;">
                        ID: {{ $kunjungan->satusehat_encounter_id }}
                    </div>
                @else
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-warning text-dark" style="font-size: 0.75rem;">
                            <i class="bi bi-exclamation-triangle me-1"></i> Belum Dikirim
                        </span>
                        <button type="button" class="btn btn-xs btn-primary py-0 px-2" style="font-size: 0.7rem;"
                            id="btnKirimEncounter" onclick="kirimEncounter('{{ $kunjungan->id ?? '' }}')">
                            <i class="bi bi-send me-1"></i> Kirim
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div> --}}
    <div class="card-body bg-light border-bottom">
        <!-- INFORMASI UTAMA PASIEN -->
        <div class="row g-2 align-items-center mb-3">
            <div class="col-md-2">
                <small class="text-muted d-block">No. Rekam Medis</small>
                <span class="fw-bold font-monospace fs-6 text-dark">{{ $pasien->no_rm }}</span>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Nama Lengkap Pasien</small>
                <span class="fw-bold text-dark">{{ $pasien->nama_lengkap ?? $pasien->nama }}</span>
                ({{ $pasien->jenis_kelamin }})
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">NIK / IHS SATUSEHAT</small>
                <span class="fw-bold text-dark">{{ $pasien->nik }}</span> /
                <span class="badge bg-info text-dark">{{ $pasien->ihs_number ?? 'Belum Bridging' }}</span>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block">Status Encounter Active</small>
                @if (isset($kunjungan) && !empty($kunjungan->satusehat_encounter_id))
                    <span class="badge bg-success font-monospace" style="font-size: 0.75rem;">
                        <i class="bi bi-check-circle me-1"></i> Terkirim
                    </span>
                    <div class="text-muted font-monospace lh-1 mt-1" style="font-size: 0.7rem;">
                        ID: {{ $kunjungan->satusehat_encounter_id }}
                    </div>
                @else
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-warning text-dark" style="font-size: 0.75rem;">
                            <i class="bi bi-exclamation-triangle me-1"></i> Belum Dikirim
                        </span>
                        <button type="button" class="btn btn-xs btn-primary py-0 px-2" style="font-size: 0.7rem;"
                            id="btnKirimEncounter" onclick="kirimEncounter('{{ $kunjungan->id ?? '' }}')">
                            <i class="bi bi-send me-1"></i> Kirim
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- TABEL RIWAYAT KUNJUNGAN -->
        <div class="border-top pt-3 mt-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">
                    <i class="bi bi-clock-history me-1"></i> Riwayat Kunjungan Pasien
                </h6>
                <span class="badge bg-secondary" style="font-size: 0.7rem;">
                    Total: {{ count($pasien->riwayatKunjungan ?? ($riwayatKunjungan ?? [])) }} Kunjungan
                </span>
            </div>

            <div class="table-responsive bg-white rounded border">
                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.8rem;"
                    id="tableRiwayat">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 40px;">#</th>
                            <th style="width: 120px;">Tgl Kunjungan</th>
                            <th>Poli / Klinik</th>
                            <th>Dokter Pemeriksa</th>
                            <th>Diagnosa (ICD-10)</th>
                            <th class="text-center" style="width: 100px;">Status</th>
                            <th class="text-center" style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pasien2->riwayatKunjungan ?? $riwayatKunjungan ?? [] as $index => $history)
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $history->poli->nama_lokasi ?? ($history->nama_poli ?? '-') }}</td>
                                <td>{{ $history->dokter->nama_dokter ?? '-' }}</td>
                                <td>
                                    @if (!empty($history->erm->icd10_code))
                                        <span
                                            class="badge bg-light text-dark border me-1">{{ $history->erm->icd10_code }}</span>
                                        <span class="text-truncate d-inline-block" style="max-width: 180px;"
                                            title="{{ $history->erm->icd10_display }}">
                                            {{ $history->erm->icd10_display }}
                                        </span>
                                    @else
                                        <span class="text-muted italic">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if (strtoupper($history->status_kunjungan ?? '') === 'SELESAI')
                                        <span class="badge bg-success" style="font-size: 0.68rem;">SELESAI</span>
                                    @else
                                        <span class="badge bg-secondary"
                                            style="font-size: 0.68rem;">{{ $history->status_kunjungan ?? 'SELESAI' }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a onclick="openDetailKunjungan('{{ $history->id }}')" href="javascript:void(0)"
                                        class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 0.7rem;"
                                        title="Lihat Detail Kunjungan">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-2">
                                    <i class="bi bi-info-circle me-1"></i> Belum ada riwayat kunjungan terdahulu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if ($kunjungan->status_kunjungan == 'SELESAI')
        <div class="alert alert-success d-flex align-items-center rounded-0 mb-0 px-4 py-2" role="alert">
            <i class="bi bi-cloud-check-fill fs-4 me-2"></i>
            <div>
                Status kunjungan <strong> {{ $kunjungan->status_kunjungan }} </strong> , Pasien sudah dipulangkan ...
            </div>
        </div>
    @endif
    <!-- Alert Notifikasi Status SATUSEHAT -->
    @if (isset($erm) && !empty($erm->satusehat_condition_id))
        <div class="alert alert-success d-flex align-items-center rounded-0 mb-0 px-4 py-2" role="alert">
            <i class="bi bi-cloud-check-fill fs-4 me-2"></i>
            <div>
                Data rekam medis ini <strong>sudah tersinkronisasi</strong> dengan SATUSEHAT Kemenkes.
            </div>
        </div>
    @else
        <div class="alert alert-light border-bottom text-muted d-flex align-items-center rounded-0 mb-0 px-4 py-2"
            role="alert">
            <i class="bi bi-info-circle fs-5 me-2 text-primary"></i>
            <div>
                Data pemeriksaan belum dikirim ke SATUSEHAT. Menekan tombol simpan akan otomatis melakukan bridging
                Condition/Diagnosa.
            </div>
        </div>
    @endif

    <!-- Form SOAP ERM -->
    <form id="formSubmitErm" onsubmit="simpanErm(event)">
        @csrf
        <input type="hidden" name="pasien_id" value="{{ $pasien->id }}">
        <input type="hidden" name="kunjungan_id" value="{{ $kunjungan->id ?? '' }}">
        <input type="hidden" name="erm_id" value="{{ $erm->id ?? '' }}">

        <!-- Hidden Input Status Bridging SATUSEHAT -->
        <input type="hidden" name="satusehat_allergy_id" value="{{ $erm->satusehat_allergy_id ?? '' }}">

        <div class="card-body">

            <!-- SECTION 1: ANAMNESIS (SUBJEKTIF) -->
            <div class="mb-4">
                <h5 class="text-dark fw-bold border-bottom pb-2">
                    <i class="bi bi-person-lines-fill me-1"></i> 1. Anamnesis (Subjektif)
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="keluhan_utama" class="form-label fw-bold fst-italic">
                            Keluhan Utama <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="keluhan_utama" name="keluhan_utama" rows="4"
                            placeholder="Contoh: Demam tinggi sejak 2 hari yang lalu..." required>{{ !empty($erm->keluhan_utama) ? $erm->keluhan_utama : $kunjungan->keluhan_utama ?? ($kunjungan->keluhan ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="riwayat_penyakit" class="form-label fw-bold fst-italic">Riwayat Penyakit
                            Dahulu/Keluarga</label>
                        <textarea class="form-control" id="riwayat_penyakit" name="riwayat_penyakit" rows="4"
                            placeholder="Contoh: Hipertensi, Diabetes Melitus...">{{ $erm->riwayat_penyakit ?? '' }}</textarea>
                    </div>
                    <!-- SUB-SECTION: RIWAYAT ALERGI (SATUSEHAT AllergyIntolerance) -->
                    <div class="col-md-12">
                        <div class="p-3 bg-light rounded border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0 text-dark">
                                    <i class="bi bi-exclamation-octagon text-danger me-1"></i> Riwayat Alergi
                                    (SATUSEHAT
                                    AllergyIntolerance)
                                </label>
                                @if (isset($erm) && !empty($erm->satusehat_allergy_id))
                                    <span class="badge bg-success font-monospace" style="font-size: 0.7rem;">
                                        <i class="bi bi-check-circle me-1"></i> Bridged (ID:
                                        {{ $erm->satusehat_allergy_id }})
                                    </span>
                                @endif
                            </div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted">Kategori Alergi</label>
                                    <select class="form-select form-select-sm" name="kategori_alergi"
                                        id="kategori_alergi">
                                        <option value="">-- Tidak Ada Alergi --</option>
                                        <option value="medication"
                                            {{ isset($erm) && $erm->kategori_alergi == 'medication' ? 'selected' : '' }}>
                                            Obat-obatan (Medication)</option>
                                        <option value="food"
                                            {{ isset($erm) && $erm->kategori_alergi == 'food' ? 'selected' : '' }}>
                                            Makanan (Food)</option>
                                        <option value="environment"
                                            {{ isset($erm) && $erm->kategori_alergi == 'environment' ? 'selected' : '' }}>
                                            Lingkungan (Debu/Dingin/dll)</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small text-muted">Detail Bahan / Nama Obat Penyebab
                                        Alergi</label>
                                    <input type="text" class="form-control form-control-sm" name="detail_alergi"
                                        id="detail_alergi" placeholder="Contoh: Amoxicillin / Ibuprofen / Seafood"
                                        value="{{ $erm->detail_alergi ?? '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: PEMERIKSAAN FISIK & TTV (OBJEKTIF) -->
            <div class="mb-4">
                <h5 class="text-primary border-bottom pb-2">
                    <i class="bi bi-activity me-1"></i> 2. Tanda-Tanda Vital & Pemeriksaan Fisik (Objektif)
                </h5>
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Sistole (mmHg)</label>
                        <input type="number" class="form-control" name="td_sistole" placeholder="120"
                            value="{{ $erm->td_sistole ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Diastole (mmHg)</label>
                        <input type="number" class="form-control" name="td_diastole" placeholder="80"
                            value="{{ $erm->td_diastole ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Nadi (x/mnt)</label>
                        <input type="number" class="form-control" name="nadi" placeholder="80"
                            value="{{ $erm->nadi ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Suhu (°C)</label>
                        <input type="number" step="0.1" class="form-control" name="suhu" placeholder="36.5"
                            value="{{ $erm->suhu ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Saturasi SpO2 (%)</label>
                        <input type="number" class="form-control" name="spo2" placeholder="98"
                            value="{{ $erm->spo2 ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Laju Napas (x/mnt)</label>
                        <input type="number" class="form-control" name="respirasi" placeholder="20"
                            value="{{ $erm->respirasi ?? '' }}">
                    </div>
                    <div class="col-md-12 mt-3">
                        <label class="form-label fw-bold">Hasil Pemeriksaan Fisik / Tambahan</label>
                        <textarea class="form-control" name="pemeriksaan_fisik" rows="2"
                            placeholder="Hasil pemeriksaan fisik kepala, dada, perut...">{{ $erm->pemeriksaan_fisik ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: DIAGNOSIS / ICD-10 (ASESMEN) -->
            <div class="mb-4">
                <h5 class="text-primary border-bottom pb-2">
                    <i class="bi bi-file-earmark-medical me-1"></i> 3. Diagnosis / Asesmen (Persiapan SATUSEHAT
                    Condition)
                </h5>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Pilih Diagnosa Utama (ICD-10) <span
                                class="text-danger">*</span></label>
                        <select class="form-select select2-icd10" id="select_icd10" style="width: 100%;" required>
                            @if (isset($erm) && $erm->icd10_code)
                                <option value="{{ $erm->icd10_code }}" selected>
                                    {{ $erm->icd10_code }} - {{ $erm->icd10_display }}
                                </option>
                            @endif
                        </select>
                        <!-- Value murni untuk disimpan ke DB -->
                        <input hidden name="icd10_code" id="icd10_code" value="{{ $erm->icd10_code ?? '' }}">
                        <input hidden name="icd10_display" id="icd10_display"
                            value="{{ $erm->icd10_display ?? '' }}">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Catatan / Diagnosa Sekunder</label>
                        <input type="text" class="form-control" name="diagnosa_catatan"
                            placeholder="Diagnosa tambahan jika ada..." value="{{ $erm->diagnosa_catatan ?? '' }}">
                    </div>
                </div>
            </div>
            <!-- SECTION 4: TINDAKAN (PROSEDUR MEDIS) & BILLING -->
            <div class="mb-4">
                <h5 class="text-primary border-bottom pb-2">
                    <i class="bi bi-journal-medical me-1"></i> 4. Tindakan / Prosedur Medis & Billing
                </h5>
                <div class="card p-3 bg-light border">
                    <!-- 1. TABEL DATA TINDAKAN YANG SUDAH DISIMPAN -->
                    @if (isset($billingDetails) && $billingDetails->count() > 0)
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-2">
                                <i class="bi bi-check2-square text-success me-1"></i> Daftar Tindakan Tersimpan
                            </label>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered bg-white align-middle">
                                    <thead class="table-dark small">
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Tindakan / Kode ICD-9-CM</th>
                                            <th style="width: 130px;" class="text-end">Tarif (Rp)</th>
                                            <th style="width: 80px;" class="text-center">Qty</th>
                                            <th style="width: 140px;" class="text-end">Subtotal (Rp)</th>
                                            <th style="width: 180px;" class="text-center">Status SATUSEHAT</th>
                                            <th style="width: 120px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalTersimpan = 0; @endphp
                                        @forelse ($billingDetails as $index => $detail)
                                            @php $totalTersimpan += $detail->subtotal; @endphp
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <span
                                                        class="fw-bold">{{ $detail->nama_tindakan ?? 'Tindakan Medis' }}</span>
                                                    @if (!empty($detail->icd9_code))
                                                        <br><small class="text-muted">ICD-9:
                                                            <code>{{ $detail->icd9_code }}</code></small>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    Rp {{ number_format($detail->harga, 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">{{ $detail->qty }}</td>
                                                <td class="text-end fw-bold">
                                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">
                                                    @if (!empty($detail->Procedure_ID))
                                                        <span class="badge bg-success"
                                                            title="ID: {{ $detail->Procedure_ID }}">
                                                            <i class="bi bi-check-circle me-1"></i> Terkirim
                                                        </span>
                                                        <br><small class="text-muted"
                                                            style="font-size: 10px;">{{ Str::limit($detail->Procedure_ID, 12) }}</small>
                                                    @elseif(!empty($detail->icd9_code))
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="bi bi-exclamation-triangle me-1"></i> Belum Kirim
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="bi bi-dash-circle me-1"></i> Non-ICD9
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{-- Tombol Batal / Hapus Tindakan --}}
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm btn-batal-tindakan"
                                                        data-id="{{ $detail->id }}"
                                                        data-nama="{{ $detail->nama_tindakan ?? 'Tindakan' }}"
                                                        data-procedure-id="{{ $detail->Procedure_ID ?? '' }}"
                                                        title="Batal / Hapus Tindakan">
                                                        <i class="bi bi-trash"></i> Retur
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-3">Belum ada
                                                    tindakan yang tersimpan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="4" class="text-end fw-bold">Subtotal Tersimpan:</th>
                                            <th class="text-end fw-bold text-success">
                                                Rp {{ number_format($totalTersimpan, 0, ',', '.') }}
                                            </th>
                                            <th colspan="2"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    <!-- 2. FORM INPUT TAMBAH TINDAKAN BARU -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0">Tambah Tindakan Baru (ICD-9-CM / Master Tarif)</label>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addTindakanRow()">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Row
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered bg-white align-middle" id="tableTindakan">
                            <thead class="table-secondary small">
                                <tr>
                                    <th>Nama Tindakan / Kode ICD-9-CM</th>
                                    <th style="width: 150px;">Tarif (Rp)</th>
                                    <th style="width: 100px;">Jumlah</th>
                                    <th style="width: 150px;">Subtotal (Rp)</th>
                                    <th style="width: 50px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tindakanContainer">
                                <!-- Row tindakan baru ditambahkan via JavaScript -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end fw-bold">Total Biaya Tindakan Baru:</th>
                                    <th colspan="2" class="fw-bold text-success" id="totalBiayaTindakan">Rp 0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            {{-- <div class="mb-4">
                <h5 class="text-primary border-bottom pb-2">
                    <i class="bi bi-capsule me-1"></i> 5. Penatalaksanaan & Resep Obat (Plan)
                </h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Rencana Tindakan / Edukasi</label>
                        <textarea class="form-control" name="tindakan_edukasi" rows="2" placeholder="Edukasi istirahat cukup...">{{ $erm->tindakan_edukasi ?? '' }}</textarea>
                    </div>
                </div>

                <!-- Tabel Input Obat / Resep Dinamis -->
                <div class="card p-3 bg-light border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0">Resep Obat (Stok & SATUSEHAT KFA)</label>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addObatRow()">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Obat
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered bg-white align-middle" id="tableObat">
                            <thead class="table-secondary small">
                                <tr>
                                    <th>Pilih Obat (Stok)</th>
                                    <th style="width: 110px;">Aturan Pakai</th>
                                    <th style="width: 90px;">Qty</th>
                                    <th style="width: 120px;">Harga Satuan</th>
                                    <th style="width: 130px;">Paket (Rp 0)</th>
                                    <th style="width: 130px;">Subtotal</th>
                                    <th style="width: 50px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="obatContainer">
                                <!-- Row obat akan ditambahkan via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> --}}
            <!-- SECTION 5: PENATALAKSANAAN & RESEP OBAT (PLAN) -->
            <div class="mb-4">
                <h5 class="text-primary border-bottom pb-2">
                    <i class="bi bi-capsule me-1"></i> 5. Penatalaksanaan & Resep Obat (Plan)
                </h5>

                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Rencana Tindakan / Edukasi</label>
                        <textarea class="form-control" name="tindakan_edukasi" rows="2" placeholder="Edukasi istirahat cukup...">{{ $erm->tindakan_edukasi ?? '' }}</textarea>
                    </div>
                </div>

                <div class="card p-3 bg-light border">
                    <!-- 1. TABEL DAFTAR RESEP OBAT YANG SUDAH DISIMPAN -->
                    @if (isset($resepDetails) && $resepDetails->count() > 0)
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-2">
                                <i class="bi bi-check2-square text-success me-1"></i> Daftar Resep Obat Tersimpan
                            </label>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered bg-white align-middle">
                                    <thead class="table-dark small">
                                        <tr>
                                            <th>#</th>
                                            <th>Nama Obat / Kode KFA</th>
                                            <th style="width: 140px;" class="text-center">Signa / Aturan</th>
                                            <th style="width: 70px;" class="text-center">Qty</th>
                                            <th style="width: 120px;" class="text-end">Harga (Rp)</th>
                                            <th style="width: 130px;" class="text-end">Subtotal (Rp)</th>
                                            <th style="width: 180px;" class="text-center">Status SATUSEHAT</th>
                                            <th style="width: 120px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalResepTersimpan = 0; @endphp
                                        @forelse ($resepDetails as $index => $resep)
                                            @php $totalResepTersimpan += $resep->subtotal; @endphp
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="fw-bold">{{ $resep->nama_obat ?? 'Obat' }}</span>
                                                    @if (!empty($resep->kfa_code))
                                                        <br><small class="text-muted">KFA:
                                                            <code>{{ $resep->kfa_code }}</code></small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge bg-info text-dark">{{ $resep->signa ?? '-' }}</span>
                                                </td>
                                                <td class="text-center fw-bold">{{ $resep->qty }}</td>
                                                <td class="text-end">
                                                    @if ($resep->is_paket ?? false)
                                                        <span class="badge bg-secondary">Paket (Rp 0)</span>
                                                    @else
                                                        Rp {{ number_format($resep->harga, 0, ',', '.') }}
                                                    @endif
                                                </td>
                                                <td class="text-end fw-bold">
                                                    Rp {{ number_format($resep->subtotal, 0, ',', '.') }}
                                                </td>
                                                <td class="text-center">
                                                    @if (!empty($resep->satusehat_medication_request_id))
                                                        <span class="badge bg-success"
                                                            title="ID: {{ $resep->satusehat_medication_request_id }}">
                                                            <i class="bi bi-check-circle me-1"></i> Terkirim
                                                        </span>
                                                        <br><small class="text-muted"
                                                            style="font-size: 10px;">{{ Str::limit($resep->satusehat_medication_request_id, 12) }}</small>
                                                    @elseif(!empty($resep->kfa_code))
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="bi bi-exclamation-triangle me-1"></i> Belum Kirim
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="bi bi-dash-circle me-1"></i> Non-KFA
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm btn-retur-obat"
                                                        data-id="{{ $resep->id }}"
                                                        data-nama="{{ $resep->nama_obat ?? 'Obat' }}"
                                                        data-med-request-id="{{ $resep->satusehat_medication_request_id ?? '' }}"
                                                        title="Retur / Batalkan Obat">
                                                        <i class="bi bi-arrow-counterclockwise"></i> Retur
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-3">Belum ada resep
                                                    obat yang tersimpan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="5" class="text-end fw-bold">Subtotal Obat Tersimpan:</th>
                                            <th class="text-end fw-bold text-success">
                                                Rp {{ number_format($totalResepTersimpan, 0, ',', '.') }}
                                            </th>
                                            <th colspan="2"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- 2. FORM INPUT TAMBAH OBAT / RESEP BARU -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0">Tambah Resep Obat Baru (Stok & SATUSEHAT KFA)</label>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addObatRow()">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Obat
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered bg-white align-middle" id="tableObat">
                            <thead class="table-secondary small">
                                <tr>
                                    <th>Pilih Obat (Stok)</th>
                                    <th style="width: 110px;">Aturan Pakai</th>
                                    <th style="width: 90px;">Qty</th>
                                    <th style="width: 120px;">Harga Satuan</th>
                                    <th style="width: 130px;">Paket (Rp 0)</th>
                                    <th style="width: 130px;">Subtotal</th>
                                    <th style="width: 50px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="obatContainer">
                                <!-- Row obat akan ditambahkan via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <!-- SECTION 5: PENANGGUNG JAWAB DOKTER -->
            <div class="mb-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Dokter Pemeriksa <span class="text-danger">*</span></label>
                        <select class="form-select" name="dokter_id" required>
                            <option value="">-- Pilih Dokter --</option>
                            @foreach ($dokters as $dok)
                                @php
                                    $selectedDokter = isset($erm) ? $erm->dokter_id : $kunjungan->dokter_id ?? '';
                                @endphp
                                <option value="{{ $dok->id }}"
                                    {{ $selectedDokter == $dok->id ? 'selected' : '' }}>
                                    {{ $dok->nama_dokter ?? $dok->nama }} (IHS: {{ $dok->ihs_number ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2 bg-light">
            <button type="button" class="btn btn-secondary" onclick="kembali()">Batal</button>
            <button type="submit" class="btn btn-success" id="btnSimpanErm">
                <i class="bi bi-save me-1"></i> {{ isset($erm) ? 'Update ERM & Bridging' : 'Simpan ERM & Bridging' }}
            </button>
        </div>
    </form>
</div>
<!-- Modal Container untuk Detail & Riwayat -->
<div class="modal fade" id="modalLayananPasien2" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalTitle">Layanan Pasien</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBodyContent2">
                <!-- Konten AJAX dimuat di sini -->
            </div>
        </div>
    </div>
</div>
<script>
    function openDetailKunjungan(pasienId) {
        $('#modalTitle').html('<i class="bi bi-person-lines-fill me-2"></i> Detail Kunjungan Pasien');
        $('#modalBodyContent2').html(
            '<div class="text-center py-5"><div class="spinner-border text-primary me-2"></div><span class="align-middle">Memuat detail...</span></div>'
        );
        $('#modalLayananPasien2').modal('show');
        $.ajax({
            url: "{{ url('/kunjungan/detail') }}/" + pasienId,
            type: "GET",
            success: function(response) {
                $('#modalBodyContent2').html(response);
            },
            error: function(xhr) {
                $('#modalBodyContent2').html(
                    '<div class="alert alert-danger m-3"><i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal memuat detail kunjungan.</div>'
                );
            }
        });
    }

    function simpanErm(event) {
        event.preventDefault();
        let btn = $('#btnSimpanErm');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');
        $.ajax({
            url: "{{ url('/erm/simpan') }}",
            type: "POST",
            data: $('#formSubmitErm').serialize(),
            success: function(response) {
                btn.prop('disabled', false).html(
                    '<i class="bi bi-save me-1"></i> Simpan ERM & Bridging Condition');
                if (response.status) {
                    alert('Data ERM & Condition SATUSEHAT berhasil disimpan!');
                    kembali();
                    loadTabelPasien(); // Refresh tabel pasien utama
                } else {
                    alert('Gagal menyimpan: ' + response.message);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(
                    '<i class="bi bi-save me-1"></i> Simpan ERM & Bridging Condition');
                alert('Terjadi kesalahan sistem saat menyimpan data ERM.');
            }
        });
    }
    $(document).ready(function() {
        $('#select_icd10').select2({
            placeholder: 'Ketik Kode (mis: A09) atau Nama Diagnosa...',
            minimumInputLength: 2,
            allowClear: true,
            ajax: {
                url: "{{ route('icd10.search') }}",
                dataType: 'json',
                delay: 250, // Debounce pencarian
                data: function(params) {
                    return {
                        q: params.term // Kata kunci pencarian
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.diag,
                                text: item.diag + ' - ' + item.nama,
                                code: item.diag, // disimpan ke property 'code'
                                display: item.nama // disimpan ke property 'display'
                            }
                        })
                    };
                },
                cache: true
            }
        });

        // Event saat item dipilih
        $('#select_icd10').on('select2:select', function(e) {
            var data = e.params.data;
            // PERBAIKAN: Panggil data.code dan data.display
            $('#icd10_code').val(data.code);
            $('#icd10_display').val(data.display);
        });

        // Event saat item dihapus (clear)
        $('#select_icd10').on('select2:unselect', function(e) {
            $('#icd10_code').val('');
            $('#icd10_display').val('');
        });
    });
    // Function Tambah Baris Tindakan
    function addTindakanRow() {
        let index = $('#tindakanContainer tr').length;
        let html = `
        <tr id="row_tindakan_${index}">
            <td>
                <!-- Select2 / Master Tarif dengan Atribut Kode ICD-9-CM -->
                <select class="form-select form-select-sm select-tindakan" name="tindakan[${index}][tarif_id]" onchange="updateHarga(this, ${index})" required>
                    <option value="">-- Pilih Tindakan --</option>
                    @foreach ($masterTarifTindakan as $tarif)
                        <option value="{{ $tarif->id }}" 
                                data-harga="{{ $tarif->harga }}" 
                                data-icd9-code="{{ $tarif->icd9_code }}" 
                                data-icd9-display="{{ $tarif->icd9_display }}">
                            {{ $tarif->nama_tindakan }} (ICD-9: {{ $tarif->icd9_code ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                <!-- Hidden Field Kode ICD-9-CM untuk Bridging SATUSEHAT -->
                <input type="hidden" name="tindakan[${index}][icd9_code]" id="icd9_code_${index}">
                <input type="hidden" name="tindakan[${index}][icd9_display]" id="icd9_display_${index}">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm harga-input" name="tindakan[${index}][harga]" id="harga_${index}" value="0" onchange="hitungSubtotal(${index})">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm qty-input" name="tindakan[${index}][qty]" id="qty_${index}" value="1" min="1" onchange="hitungSubtotal(${index})">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm subtotal-input" name="tindakan[${index}][subtotal]" id="subtotal_${index}" value="0" onchange="hitungSubtotal(${index})">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTindakanRow(${index})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `;
        $('#tindakanContainer').append(html);
    }

    function updateHarga(select, index) {
        let selectedOption = $(select).find(':selected');
        let harga = selectedOption.data('harga') || 0;
        let icd9Code = selectedOption.data('icd9-code') || '';
        let icd9Display = selectedOption.data('icd9-display') || '';

        $('#harga_' + index).val(harga);
        $('#icd9_code_' + index).val(icd9Code);
        $('#icd9_display_' + index).val(icd9Display);

        hitungSubtotal(index);
    }

    function hitungSubtotal(index) {
        let harga = parseFloat($('#harga_' + index).val()) || 0;
        let qty = parseInt($('#qty_' + index).val()) || 1;
        let subtotal = harga * qty;

        $('#subtotal_' + index).val(subtotal);
        hitungTotalSemua();
    }

    function hitungTotalSemua() {
        let total = 0;
        $('.subtotal-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $('#totalBiayaTindakan').text('Rp ' + total.toLocaleString('id-ID'));
    }

    function removeTindakanRow(index) {
        $('#row_tindakan_' + index).remove();
        hitungTotalSemua();
    }

    // function addObatRow() {
    //     let index = $('#obatContainer tr').length;
    //     let html = `
    //     <tr id="row_obat_${index}">
    //         <td>
    //             <select class="form-select form-select-sm select-obat" name="resep[${index}][obat_id]" onchange="updateObatInfo(this, ${index})" required>
    //                 <option value="">-- Pilih Obat --</option>
    //                 @foreach ($masterObat ?? [] as $obat)
    //                     <option value="{{ $obat->id }}" 
    //                             data-harga="{{ $obat->harga_jual }}" 
    //                             data-stok="{{ $obat->stok }}" 
    //                             data-kfa-code="{{ $obat->kfa_code }}" 
    //                             data-kfa-display="{{ $obat->kfa_display }}"
    //                             data-satuan="{{ $obat->satuan }}">
    //                         {{ $obat->nama_obat }} (Stok: {{ $obat->stok }} {{ $obat->satuan }})
    //                     </option>
    //                 @endforeach
    //             </select>
    //             <!-- Hidden Fields SATUSEHAT -->
    //             <input type="hidden" name="resep[${index}][kfa_code]" id="kfa_code_${index}">
    //             <input type="hidden" name="resep[${index}][kfa_display]" id="kfa_display_${index}">
    //             <input type="hidden" name="resep[${index}][nama_obat]" id="nama_obat_${index}">
    //         </td>
    //         <td>
    //             <input type="text" class="form-control form-control-sm" name="resep[${index}][signa]" placeholder="3x1 p.c" required>
    //         </td>
    //         <td>
    //             <input type="number" class="form-control form-control-sm qty-obat" name="resep[${index}][qty]" id="qty_obat_${index}" value="1" min="1" onchange="hitungSubtotalObat(${index})">
    //         </td>
    //         <td>
    //             <input type="number" class="form-control form-control-sm harga-obat" name="resep[${index}][harga]" id="harga_obat_${index}" readonly value="0">
    //         </td>
    //         <td class="text-center">
    //             <div class="form-check form-switch d-flex justify-content-center">
    //                 <input class="form-check-input" type="checkbox" name="resep[${index}][is_paket]" value="1" id="is_paket_${index}" onchange="togglePaketObat(${index})">
    //                 <label class="form-check-label ms-1 small" for="is_paket_${index}">Gratis/Paket</label>
    //             </div>
    //         </td>
    //         <td>
    //             <input type="number" class="form-control form-control-sm subtotal-obat" name="resep[${index}][subtotal]" id="subtotal_obat_${index}" readonly value="0">
    //         </td>
    //         <td class="text-center">
    //             <button type="button" class="btn btn-sm btn-outline-danger" onclick="$('#row_obat_${index}').remove();">
    //                 <i class="bi bi-trash"></i>
    //             </button>
    //         </td>
    //     </tr>
    // `;
    //     $('#obatContainer').append(html);
    // }
    function addObatRow() {
        let index = $('#obatContainer tr').length;
        let html = `
    <tr id="row_obat_${index}">
        <td>
            <select class="form-select form-select-sm select-obat" name="resep[${index}][obat_id]" id="obat_id_${index}" onchange="updateObatInfo(this, ${index})" required>
                <option value="">-- Ketik Nama / Pilih Obat --</option>
                @foreach ($masterObat ?? [] as $obat)
                    <option value="{{ $obat->id }}" 
                            data-nama="{{ $obat->nama_obat }}"
                            data-harga="{{ $obat->harga_jual }}" 
                            data-stok="{{ $obat->stok }}" 
                            data-kfa-code="{{ $obat->kfa_code }}" 
                            data-kfa-display="{{ $obat->kfa_display }}"
                            data-satuan="{{ $obat->satuan }}">
                        {{ $obat->nama_obat }} (Stok: {{ $obat->stok }} {{ $obat->satuan }})
                    </option>
                @endforeach
            </select>
            <!-- Hidden Fields SATUSEHAT -->
            <input type="hidden" name="resep[${index}][kfa_code]" id="kfa_code_${index}">
            <input type="hidden" name="resep[${index}][kfa_display]" id="kfa_display_${index}">
            <input type="hidden" name="resep[${index}][nama_obat]" id="nama_obat_${index}">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="resep[${index}][signa]" placeholder="3x1 p.c" required>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm qty-obat" name="resep[${index}][qty]" id="qty_obat_${index}" value="1" min="1" onchange="hitungSubtotalObat(${index})">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm harga-obat" name="resep[${index}][harga]" id="harga_obat_${index}" value="0" onchange="hitungSubtotalObat(${index})">
        </td>
        <td class="text-center">
            <div class="form-check form-switch d-flex justify-content-center">
                <input class="form-check-input" type="checkbox" name="resep[${index}][is_paket]" value="1" id="is_paket_${index}" onchange="togglePaketObat(${index})">
                <label class="form-check-label ms-1 small" for="is_paket_${index}">Gratis/Paket</label>
            </div>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm subtotal-obat" name="resep[${index}][subtotal]" id="subtotal_obat_${index}" readonly value="0">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="$('#row_obat_${index}').remove(); hitungTotalKeseluruhan();">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    </tr>
    `;

        // 1. Append HTML ke tabel
        $('#obatContainer').append(html);

        // 2. Inisialisasi Select2 pada element yang baru dibuat
        $(`#obat_id_${index}`).select2({
            placeholder: "-- Ketik Nama / Pilih Obat --",
            allowClear: true,
            width: '100%'
        });
    }

    // function updateObatInfo(select, index) {
    //     let opt = $(select).find(':selected');
    //     let harga = opt.data('harga') || 0;

    //     $('#harga_obat_' + index).val(harga);
    //     $('#kfa_code_' + index).val(opt.data('kfa-code') || '');
    //     $('#kfa_display_' + index).val(opt.data('kfa-display') || '');
    //     $('#nama_obat_' + index).val(opt.text().split('(Stok')[0].trim());

    //     hitungSubtotalObat(index);
    // }
    function updateObatInfo(element, index) {
        let selectedOption = $(element).find(':selected');

        let harga = selectedOption.data('harga') || 0;
        let kfaCode = selectedOption.data('kfa-code') || '';
        let kfaDisplay = selectedOption.data('kfa-display') || '';
        let namaObat = selectedOption.data('nama') || '';

        // Set nilai ke input hidden & harga
        $(`#harga_obat_${index}`).val(harga);
        $(`#kfa_code_${index}`).val(kfaCode);
        $(`#kfa_display_${index}`).val(kfaDisplay);
        $(`#nama_obat_${index}`).val(namaObat);

        // Hitung ulang subtotal baris tersebut
        hitungSubtotalObat(index);
    }

    function togglePaketObat(index) {
        let isPaket = $('#is_paket_' + index).is(':checked');
        if (isPaket) {
            $('#subtotal_obat_' + index).val(0);
        } else {
            hitungSubtotalObat(index);
        }
    }

    function hitungSubtotalObat(index) {
        let isPaket = $('#is_paket_' + index).is(':checked');
        if (isPaket) {
            $('#subtotal_obat_' + index).val(0);
            return;
        }
        let harga = parseFloat($('#harga_obat_' + index).val()) || 0;
        let qty = parseInt($('#qty_obat_' + index).val()) || 1;
        $('#subtotal_obat_' + index).val(harga * qty);
    }

    function kirimEncounter(kunjunganId) {
        if (!kunjunganId) {
            alert('ID Kunjungan tidak ditemukan!');
            return;
        }

        const btn = document.getElementById('btnKirimEncounter');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';

        fetch("{{ route('satusehat.encounter.send', '') }}/" + kunjunganId, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    alert('Encounter berhasil dikirim! ID: ' + data.encounter_id);
                    location.reload(); // Refresh halaman untuk memperbarui status badge
                } else {
                    alert('Gagal mengirim Encounter: ' + (data.message || JSON.stringify(data.error)));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-send me-1"></i> Kirim';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi/server.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-send me-1"></i> Kirim';
            });
    }
    $(document).ready(function() {
        $(document).on('click', '.btn-batal-tindakan', function(e) {
            e.preventDefault();

            let idDetail = $(this).data('id');
            let namaTindakan = $(this).data('nama');
            let procedureId = $(this).data('procedure-id');

            let warningMsg = `Apakah Anda yakin ingin membatalkan tindakan <b>${namaTindakan}</b>?`;

            // Proteksi peringatan jika data sudah terkirim ke SATUSEHAT
            if (procedureId) {
                warningMsg +=
                    `<br><br><small class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Perhatian: Tindakan ini sudah terintegrasi dengan SATUSEHAT (${procedureId}). Membatalkan tindakan di sini juga perlu penanganan pada resource Procedure di SATUSEHAT.</small>`;
            }

            Swal.fire({
                title: 'Batalkan Tindakan?',
                html: warningMsg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Kembali'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('billing.batal_tindakan') }}", // Sesuaikan nama route milikmu
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: idDetail
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Mohon tunggu sebentar',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message ||
                                        'Tindakan berhasil dibatalkan.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location
                                        .reload(); // Atau panggil fungsi refresh table AJAX kamu
                                });
                            } else {
                                Swal.fire('Gagal!', response.message ||
                                    'Gagal membatalkan tindakan.', 'error');
                            }
                        },
                        error: function(xhr) {
                            let res = xhr.responseJSON;
                            Swal.fire('Error!', res?.message ||
                                'Terjadi kesalahan sistem.', 'error');
                        }
                    });
                }
            });
        });
    });
    $(document).ready(function() {
        $(document).on('click', '.btn-retur-obat', function(e) {
            e.preventDefault();

            let idDetail = $(this).data('id');
            let namaObat = $(this).data('nama');
            let medRequestId = $(this).data('med-request-id');

            let warningMsg =
                `Apakah Anda yakin ingin meretur/membatalkan obat <b>${namaObat}</b>? Stok obat akan dikembalikan.`;

            if (medRequestId) {
                warningMsg +=
                    `<br><br><small class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Data obat ini sudah terkirim ke SATUSEHAT (MedicationRequest: ${medRequestId}).</small>`;
            }

            Swal.fire({
                title: 'Retur Obat?',
                html: warningMsg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Retur Obat!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('resep.retur_obat') }}", // Sesuaikan nama route Anda
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: idDetail
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Mengembalikan stok & membatalkan obat',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message ||
                                        'Obat berhasil diretur.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal!', response.message ||
                                    'Gagal meretur obat.', 'error');
                            }
                        },
                        error: function(xhr) {
                            let res = xhr.responseJSON;
                            Swal.fire('Gagal!', res?.message ||
                                'Terjadi kesalahan sistem.', 'error');
                        }
                    });
                }
            });
        });
    });
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
</script>
