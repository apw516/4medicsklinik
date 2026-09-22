<div class="card border-0 shadow-sm">
    <!-- Header Form -->
    <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold">
            <i class="bi bi-person-plus-fill me-2"></i>Form Pendaftaran Pelayanan Pasien
        </h5>
        <button type="button" class="btn btn-light btn-sm fw-bold btn-kembali">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Pencarian
        </button>
    </div>

    <div class="card-body p-4">
        <!-- SECTION 1: DETAIL PASIEN -->
        <div class="border-bottom pb-3 mb-4">
            <h6 class="fw-bold text-primary mb-3">
                <i class="bi bi-person-vcard me-2"></i>1. Detail Identitas Pasien
            </h6>
            <div class="bg-light p-3 rounded border">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1 fw-bold">No. Rekam Medis (RM)</label>
                        <input type="text" class="form-control form-control-sm bg-white fw-bold text-primary"
                            id="detail_no_rm" readonly placeholder="-" value="{{ $pasien->no_rm }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1 fw-bold">No. KTP / NIK</label>
                        <input type="text" class="form-control form-control-sm bg-white" id="detail_nik" readonly
                            placeholder="-" value="{{ $pasien->nik }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small mb-1 fw-bold">Nama Lengkap Pasien</label>
                        <input type="text" class="form-control form-control-sm bg-white fw-bold" id="detail_nama"
                            readonly placeholder="-" value="{{ $pasien->nama_lengkap }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-muted small mb-1 fw-bold">Jenis Kelamin</label>
                        <input type="text" class="form-control form-control-sm bg-white" id="detail_jk" readonly
                            placeholder="-" value="{{ $pasien->jenis_kelamin }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1 fw-bold">Tanggal Lahir / Umur</label>
                        <input type="text" class="form-control form-control-sm bg-white" id="detail_tgl_lahir"
                            readonly placeholder="-" value="{{ $pasien->tanggal_lahir }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small mb-1 fw-bold">No. HP / WhatsApp</label>
                        <input type="text" class="form-control form-control-sm bg-white" id="detail_no_hp" readonly
                            placeholder="-" value="{{ $pasien->no_hp }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-1 fw-bold">Alamat Pasien</label>
                        <input type="text" class="form-control form-control-sm bg-white" id="detail_alamat" readonly
                            placeholder="-" value="{{ $pasien->alamat }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: FORM PENDAFTARAN -->
        <form id="formPendaftaranPelayanan" class="border-bottom pb-4 mb-4">
            <!-- Hidden Input Pasien ID -->
            <input type="hidden" name="pasien_id" id="pasien_id" value="{{ $pasien->id }}">

            <h6 class="fw-bold text-primary mb-3">
                <i class="bi bi-clipboard2-pulse me-2"></i>2. Data Kunjungan & Pelayanan (SIMRS & SATUSEHAT)
            </h6>

            <div class="row g-3">
                <!-- Jenis Kunjungan & SATUSEHAT Class -->
                <div class="col-md-3">
                    <label for="jenis_kunjungan" class="form-label fw-bold small">Jenis Kunjungan <span
                            class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="jenis_kunjungan" name="jenis_kunjungan">
                        <option value="RAWAT_JALAN">Rawat Jalan (AMB)</option>
                        <option value="RAWAT_INAP">Rawat Inap (IMP)</option>
                        <option value="IGD">IGD / Emergency (EMER)</option>
                    </select>
                </div>

                <!-- Hidden Input Mapping SATUSEHAT Class -->
                <input type="hidden" name="satusehat_class" id="satusehat_class" value="AMB">
                <input type="hidden" name="satusehat_status" id="satusehat_status" value="arrived">

                <!-- Cara Masuk -->
                <div class="col-md-3">
                    <label for="cara_masuk" class="form-label fw-bold small">Cara Masuk <span
                            class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="cara_masuk" name="cara_masuk">
                        <option value="DATANG_SENDIRI">Datang Sendiri</option>
                        <option value="RUJUKAN_FKTP">Rujukan FKTP (Puskesmas/Klinik)</option>
                        <option value="RUJUKAN_FKRTL">Rujukan FKRTL (Rujukan RS)</option>
                        <option value="AMBULANS">Ambulans</option>
                        <option value="LAINNYA">Lainnya</option>
                    </select>
                </div>

                <!-- Tanggal Masuk -->
                <div class="col-md-3">
                    <label for="tgl_masuk" class="form-label fw-bold small">Tanggal & Waktu Masuk <span
                            class="text-danger">*</span></label>
                    <input type="datetime-local" class="form-control form-control-sm" id="tgl_masuk"
                        name="tgl_masuk" value="{{ date('Y-m-d\TH:i') }}">
                </div>

                <!-- Penjamin / Cara Bayar -->
                <div class="col-md-3">
                    <label for="penjamin" class="form-label fw-bold small">Penjamin / Cara Bayar <span
                            class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="penjamin" name="penjamin">
                        <option value="UMUM">UMUM / Mandiri</option>
                        <option value="BPJS">BPJS Kesehatan</option>
                        <option value="ASURANSI_SWASTA">Asuransi Swasta</option>
                        <option value="PERUSAHAAN">Perusahaan</option>
                    </select>
                </div>

                <!-- Poliklinik Tujuan -->
                <div class="col-md-4">
                    <label for="poli_id" class="form-label fw-bold small">Poliklinik / Unit Tujuan <span
                            class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="poli_id" name="poli_id">
                        <option value="">-- Pilih Poliklinik --</option>
                        @foreach ($unit as $u)
                            <option value="{{ $u->id }}">{{ $u->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Dokter DPJP -->
                <div class="col-md-4">
                    <label for="dokter_id" class="form-label fw-bold small">Dokter DPJP <span
                            class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="dokter_id" name="dokter_id">
                        <option value="">-- Pilih Dokter DPJP --</option>
                        @foreach ($dokter as $u)
                            <option value="{{ $u->id }}">{{ $u->nama_dokter }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Keluhan Utama -->
                <div class="col-md-4">
                    <label for="keluhan_utama" class="form-label fw-bold small">Keluhan Utama</label>
                    <input type="text" class="form-control form-control-sm" id="keluhan_utama"
                        name="keluhan_utama" placeholder="Alasan utama berobat / keluhan">
                </div>
            </div>

            <!-- SECTION BPJS KESEHATAN (Dinamis: Muncul hanya jika Penjamin = BPJS) -->
            <div id="container_bpjs" class="mt-4 p-3 bg-light border border-info rounded" style="display: none;">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-success me-2 px-2 py-1">BPJS</span>
                    <h6 class="fw-bold text-success m-0">
                        <i class="bi bi-card-checklist me-1"></i> Parameter Integration BPJS Kesehatan (V-Claim)
                    </h6>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="no_sep" class="form-label fw-bold small">No. SEP (Surat Eligibilitas
                            Peserta)</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" id="no_sep" name="no_sep"
                                placeholder="Contoh: 0001R0010426V000001">
                            <button class="btn btn-outline-success" type="button" id="btnCekSep"
                                title="Cek/Cetak SEP BPJS">
                                <i class="bi bi-search"></i> Cek
                            </button>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="no_rujukan" class="form-label fw-bold small">No. Rujukan Asal</label>
                        <input type="text" class="form-control form-control-sm" id="no_rujukan" name="no_rujukan"
                            placeholder="19 Digit No Rujukan FKTP/FKRTL">
                    </div>

                    <div class="col-md-4">
                        <label for="no_surat_kontrol" class="form-label fw-bold small">No. Surat Kontrol /
                            SKDP</label>
                        <input type="text" class="form-control form-control-sm" id="no_surat_kontrol"
                            name="no_surat_kontrol" placeholder="6 Digit No Surat Kontrol">
                    </div>

                    <div class="col-md-3">
                        <label for="kelas_rawat_bpjs" class="form-label fw-bold small">Kelas Hak Akses BPJS</label>
                        <select class="form-select form-select-sm" id="kelas_rawat_bpjs" name="kelas_rawat_bpjs">
                            <option value="">-- Pilih Kelas --</option>
                            <option value="1">Kelas 1</option>
                            <option value="2">Kelas 2</option>
                            <option value="3">Kelas 3</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="diag_awal_bpjs" class="form-label fw-bold small">Diagnosa Awal (ICD-10
                            BPJS)</label>
                        <input type="text" class="form-control form-control-sm" id="diag_awal_bpjs"
                            name="diag_awal_bpjs" placeholder="Contoh: A09 (Gastroenteritis)">
                    </div>

                    <div class="col-md-5">
                        <label for="catatan_sep" class="form-label fw-bold small">Catatan SEP</label>
                        <input type="text" class="form-control form-control-sm" id="catatan_sep"
                            name="catatan_sep" placeholder="Catatan khusus dari petugas BPJS">
                    </div>
                </div>
            </div>
        </form>
        <!-- SECTION 3: RIWAYAT KUNJUNGAN -->
        <div>
            <h6 class="fw-bold text-primary mb-3">
                <i class="bi bi-clock-history me-2"></i>3. Riwayat Kunjungan Pasien
            </h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle mb-0"
                    style="font-size: 0.85rem;" id="tableRiwayat">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 4%;">No</th>
                            <th style="width: 14%;">Tgl. Kunjungan</th>
                            <th style="width: 16%;">No. Registrasi</th>
                            <th style="width: 16%;">Poli / Unit</th>
                            <th style="width: 18%;">Dokter DPJP</th>
                            <th style="width: 12%;">Penjamin / SEP</th>
                            <th class="text-center" style="width: 10%;">SATUSEHAT</th>
                            <th class="text-center" style="width: 10%;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="v_tabel_riwayat">
                        @forelse ($data_kunjungan as $index => $kunjungan)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($kunjungan->tgl_kunjungan)->format('d-m-Y H:i') }}</td>
                                <td>
                                    <span
                                        class="badge bg-light text-dark border">{{ $kunjungan->no_registrasi }}</span>
                                </td>
                                <td>{{ $kunjungan->unit->nama_unit ?? '-' }}</td>
                                <td>{{ $kunjungan->dokter->nama_dokter ?? '-' }}</td>
                                <td>
                                    <div>{{ $kunjungan->penjamin ?? '-' }}</div>
                                    @if (!empty($kunjungan->no_sep))
                                        <small class="text-muted">SEP: {{ $kunjungan->no_sep }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($kunjungan->satusehat_encounter_id)
                                        <span class="badge bg-success"><i
                                                class="bi bi-check-circle me-1"></i>TerKirim</span>
                                    @else
                                        <span class="badge bg-secondary">Belum</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($kunjungan->status_kunjungan == 'SELESAI' || $kunjungan->status_kunjungan == 'Selesai')
                                        <span class="badge bg-primary">Selesai</span>
                                    @else
                                        <span
                                            class="badge bg-warning text-dark">{{ $kunjungan->status_kunjungan }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <!-- State Data Kosong -->
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                    Belum ada riwayat kunjungan pasien.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Footer Action Buttons -->
    <div class="card-footer bg-white text-end py-3">
        <button type="button" class="btn btn-secondary btn-sm me-1 btn-kembali">
            <i class="bi bi-x-circle me-1"></i> Batal
        </button>
        <button type="button" id="btnSimpanPendaftaran" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Simpan Pendaftaran
        </button>
    </div>
</div>
<script>
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
    $(document).ready(function() {
        // 1. Tombol Kembali
        $(document).on('click', '.btn-kembali', function() {
            $('.v_2').prop('hidden', true);
            $('.v_1').prop('hidden', false);
            $('.v_formnya').html('');
        });

        // 2. Toggle Form BPJS berdasarkan Pilihan Penjamin
        $(document).on('change', '#penjamin', function() {
            let penjamin = $(this).val();
            if (penjamin === 'BPJS') {
                $('#container_bpjs').slideDown(200);
            } else {
                $('#container_bpjs').slideUp(200);
                // Reset input BPJS
                $('#no_sep, #no_rujukan, #no_surat_kontrol, #diag_awal_bpjs, #catatan_sep').val('');
                $('#kelas_rawat_bpjs').val('');
            }
        });

        // 3. Auto Mapping SATUSEHAT Class berdasarkan Jenis Kunjungan
        $(document).on('change', '#jenis_kunjungan', function() {
            let jenis = $(this).val();
            if (jenis === 'RAWAT_JALAN') {
                $('#satusehat_class').val('AMB');
            } else if (jenis === 'RAWAT_INAP') {
                $('#satusehat_class').val('IMP');
            } else if (jenis === 'IGD') {
                $('#satusehat_class').val('EMER');
            }
        });

        // 4. Proses Simpan Pendaftaran Pelayanan via AJAX
        // Gunakan .off('click') untuk mencegah event terdaftar ganda
        $(document).off('click', '#btnSimpanPendaftaran').on('click', '#btnSimpanPendaftaran', function(e) {
            e.preventDefault();

            let btn = $(this);

            // Mencegah klik ganda jika tombol sedang dalam proses loading
            if (btn.is(':disabled')) {
                return false;
            }

            let form = $('#formPendaftaranPelayanan');

            // Pastikan _token disertakan secara aman (dari form atau meta tag)
            let formData = form.serialize();
            if (formData.indexOf('_token=') === -1) {
                formData += '&_token=' + encodeURIComponent('{{ csrf_token() }}');
            }

            // Set UI Loading State
            btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...'
            );

            $.ajax({
                url: "{{ route('pendaftaran.store') }}",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function(response) {
                    alert('Pendaftaran Berhasil Disimpan!');

                    // Kembalikan tombol ke keadaan semula jika ada aksi modal/kembali
                    btn.prop('disabled', false).html(
                        '<i class="bi bi-save me-1"></i> Simpan Pendaftaran'
                    );

                    // Trigger tombol kembali / reset UI
                    $('.btn-kembali').trigger('click');
                },
                error: function(xhr) {
                    // Restore UI State saat error
                    btn.prop('disabled', false).html(
                        '<i class="bi bi-save me-1"></i> Simpan Pendaftaran'
                    );

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = 'Validasi Gagal:\n';
                        $.each(errors, function(key, value) {
                            errorMsg += '- ' + value[0] + '\n';
                        });
                        alert(errorMsg);
                    } else {
                        let msg = xhr.responseJSON && xhr.responseJSON.message ?
                            xhr.responseJSON.message :
                            'Terjadi kesalahan saat menyimpan data pendaftaran.';
                        alert(msg);
                    }
                }
            });
        });
    });
</script>
