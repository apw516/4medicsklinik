{{-- <form action="{{ route('pasien.store') }}" method="POST"> --}}
    @csrf
    <div class="modal-body">
        <!-- Section 1: Identitas SIMRS, SATUSEHAT & BPJS -->
        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
            <i class="bi bi-card-heading me-1"></i> Identitas Sistem & Integrasi
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="add_no_rm" class="form-label fw-bold">No. RM</label>
                <input readonly type="text" class="form-control form-control-sm" id="add_no_rm" name="no_rm"
                    value="{{ $pasien->no_rm }}" placeholder="00-00-00-01">
            </div>
            <div class="col-md-3">
                <label for="add_nik" class="form-label fw-bold">NIK / No. KTP <span class="text-danger">*</span></label>
                <input readonly type="text" class="form-control form-control-sm @error('nik') is-invalid @enderror" id="add_nik"
                    name="nik" value="{{ $pasien->nik }}" maxlength="16" required placeholder="16 Digit NIK">
            </div>
            <div class="col-md-3">
                <label for="add_no_bpjs" class="form-label">No. Kartu BPJS</label>
                <input type="text" class="form-control form-control-sm @error('no_bpjs') is-invalid @enderror"
                    id="add_no_bpjs" name="no_bpjs" value="{{ $pasien->no_bpjs }}" maxlength="13"
                    placeholder="13 Digit No. BPJS">
            </div>
            <div class="col-md-3">
                <label for="add_ihs_number" class="form-label">IHS Number SATUSEHAT</label>
                <input type="text" class="form-control form-control-sm @error('ihs_number') is-invalid @enderror"
                    id="add_ihs_number" name="ihs_number" value="{{ $pasien->ihs_number }}"
                    placeholder="Otomatis / Manual">
            </div>
        </div>

        <!-- Section 2: Data Diri Utama -->
        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
            <i class="bi bi-person-vcard me-1"></i> Data Diri Pasien
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-2">
                <label for="add_gelar_depan" class="form-label">Gelar Depan</label>
                <input type="text" class="form-control form-control-sm" id="add_gelar_depan" name="gelar_depan"
                    value="{{ $pasien->gelar_depan }}" placeholder="dr. / H.">
            </div>
            <div class="col-md-6">
                <label for="add_nama_lengkap" class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="add_nama_lengkap" name="nama_lengkap"
                    value="{{ $pasien->nama_lengkap }}" required placeholder="Nama lengkap sesuai KTP">
            </div>
            <div class="col-md-4">
                <label for="add_gelar_belakang" class="form-label">Gelar Belakang</label>
                <input type="text" class="form-control form-control-sm" id="add_gelar_belakang"
                    name="gelar_belakang" value="{{ $pasien->gelar_belakang }}" placeholder="S.Kom. / M.Kes.">
            </div>

            <div class="col-md-3">
                <label for="add_jenis_kelamin" class="form-label fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm" id="add_jenis_kelamin" name="jenis_kelamin" required>
                    <option value="">-- Pilih --</option>
                    <option value="L" {{ $pasien->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ $pasien->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="add_tempat_lahir" class="form-label fw-bold">Tempat Lahir <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" id="add_tempat_lahir" name="tempat_lahir"
                    value="{{ $pasien->tempat_lahir }}" required placeholder="Kota lahir">
            </div>
            <div class="col-md-3">
                <label for="add_tanggal_lahir" class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date" class="form-control form-control-sm" id="add_tanggal_lahir"
                    name="tanggal_lahir" value="{{ $pasien->tanggal_lahir }}" required>
            </div>
            <div class="col-md-1">
                <label for="add_golongan_darah" class="form-label">Gol. Darah</label>
                <select class="form-select form-select-sm" id="add_golongan_darah" name="golongan_darah">
                    <option value="">-</option>
                    <option value="A" {{ $pasien->golongan_darah == 'A' ? 'selected' : '' }}>A</option>
                    <option value="B" {{ $pasien->golongan_darah == 'B' ? 'selected' : '' }}>B</option>
                    <option value="AB" {{ $pasien->golongan_darah == 'AB' ? 'selected' : '' }}>AB</option>
                    <option value="O" {{ $pasien->golongan_darah == 'O' ? 'selected' : '' }}>O</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="add_rhesus" class="form-label">Rhesus</label>
                <select class="form-select form-select-sm" id="add_rhesus" name="rhesus">
                    <option value="">-</option>
                    <option value="+" {{ $pasien->rhesus == '+' ? 'selected' : '' }}>Positif (+)</option>
                    <option value="-" {{ $pasien->rhesus == '-' ? 'selected' : '' }}>Negatif (-)</option>
                </select>
            </div>
        </div>

        <!-- Section 3: Profil Demografi & Kontak -->
        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
            <i class="bi bi-person-lines-fill me-1"></i> Demografi & Kontak
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="add_agama" class="form-label">Agama</label>
                <select class="form-select form-select-sm" id="add_agama" name="agama">
                    <option value="">-- Pilih Agama --</option>
                    @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                        <option value="{{ $agm }}" {{ $pasien->agama == $agm ? 'selected' : '' }}>{{ $agm }}</option>
                    @endforeach
                </select>
                @error('agama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-3">
                <label for="add_status_pernikahan" class="form-label">Status Pernikahan</label>
                <select class="form-select form-select-sm" id="add_status_pernikahan" name="status_pernikahan">
                    <option value="">-- Pilih Status --</option>
                    <option value="S" {{ $pasien->status_pernikahan == 'S' ? 'selected' : '' }}>Belum Menikah (Single)</option>
                    <option value="M" {{ $pasien->status_pernikahan == 'M' ? 'selected' : '' }}>Menikah (Married)</option>
                    <option value="D" {{ $pasien->status_pernikahan == 'D' ? 'selected' : '' }}>Cerai Hidup (Divorced)</option>
                    <option value="W" {{ $pasien->status_pernikahan == 'W' ? 'selected' : '' }}>Cerai Mati (Widowed)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="add_pekerjaan" class="form-label">Pekerjaan</label>
                <input type="text" class="form-control form-control-sm" id="add_pekerjaan" name="pekerjaan"
                    value="{{ $pasien->pekerjaan }}" placeholder="PNS/Swasta/Wiraswasta">
            </div>
            <div class="col-md-3">
                <label for="add_pendidikan" class="form-label">Pendidikan</label>
                <select class="form-select form-select-sm" id="add_pendidikan" name="pendidikan">
                    <option value="">-- Pilih Pendidikan --</option>
                    @foreach (['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'] as $pdk)
                        <option value="{{ $pdk }}" {{ $pasien->pendidikan == $pdk ? 'selected' : '' }}>{{ $pdk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="add_kewarganegaraan" class="form-label">Kewarganegaraan</label>
                <select class="form-select form-select-sm" id="add_kewarganegaraan" name="kewarganegaraan">
                    <option value="WNI" {{ $pasien->kewarganegaraan == 'WNI' ? 'selected' : '' }}>WNI</option>
                    <option value="WNA" {{ $pasien->kewarganegaraan == 'WNA' ? 'selected' : '' }}>WNA</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="add_no_hp" class="form-label">No. HP / Whatsapp</label>
                <input type="text" class="form-control form-control-sm" id="add_no_hp" name="no_hp"
                    value="{{ $pasien->no_hp }}" placeholder="08123456789">
            </div>
            <div class="col-md-6">
                <label for="add_email" class="form-label">Email Pasien</label>
                <input type="email" class="form-control form-control-sm" id="add_email" name="email"
                    value="{{ $pasien->email }}" placeholder="contoh@email.com">
            </div>
        </div>

        <!-- Section 4: Alamat Domisili -->
        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
            <i class="bi bi-geo-alt me-1"></i> Alamat Domisili
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="add_alamat" class="form-label fw-bold">Alamat Lengkap <span class="text-danger">*</span></label>
                <textarea class="form-control form-control-sm" id="add_alamat" name="alamat" rows="3" required
                    placeholder="Jalan, No. Rumah, Blok, Gang">{{ $pasien->alamat }}</textarea>
            </div>
            <div class="col-md-6">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label for="add_rt" class="form-label">RT</label>
                        <input type="text" class="form-control form-control-sm" id="add_rt"
                            name="rt" value="{{ $pasien->rt }}" placeholder="001">
                    </div>
                    <div class="col-md-4">
                        <label for="add_rw" class="form-label">RW</label>
                        <input type="text" class="form-control form-control-sm" id="add_rw" name="rw"
                            value="{{ $pasien->rw }}" placeholder="002">
                    </div>
                    <div class="col-md-4">
                        <label for="add_kode_pos" class="form-label">Kode Pos</label>
                        <input type="text" class="form-control form-control-sm" id="add_kode_pos"
                            name="kode_pos" value="{{ $pasien->kode_pos }}" placeholder="45188">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <label for="add_provinsi_code_1" class="form-label">Provinsi</label>
                <select class="form-select form-select-sm" id="add_provinsi_code_1" name="provinsi_code">
                    <option value="">-- Pilih Provinsi --</option>
                    @foreach ($listProvinsi as $prov)
                        <option value="{{ $prov->code }}" {{ $pasien->provinsi_code == $prov->code ? 'selected' : '' }}>
                            {{ $prov->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="add_kabkota_code_1" class="form-label">Kabupaten / Kota</label>
                <select class="form-select form-select-sm" id="add_kabkota_code_1" name="kabkota_code" disabled>
                    <option value="">-- Pilih Kab/Kota --</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="add_kecamatan_code_1" class="form-label">Kecamatan</label>
                <select class="form-select form-select-sm @error('kecamatan_code') is-invalid @enderror"
                    id="add_kecamatan_code_1" name="kecamatan_code" disabled>
                    <option value="">-- Pilih Kecamatan --</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="add_kelurahan_code_1" class="form-label">Kelurahan / Desa</label>
                <select class="form-select form-select-sm @error('kelurahan_code') is-invalid @enderror"
                    id="add_kelurahan_code_1" name="kelurahan_code" disabled>
                    <option value="">-- Pilih Kelurahan --</option>
                </select>
            </div>
        </div>

        <!-- Section 5: Penanggung Jawab -->
        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
            <i class="bi bi-telephone-outbound me-1"></i> Penanggung Jawab / Kontak Darurat
        </h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label for="add_nama_pj" class="form-label">Nama Penanggung Jawab</label>
                <input type="text" class="form-control form-control-sm @error('nama_pj') is-invalid @enderror"
                    id="add_nama_pj" name="nama_pj" value="{{ $pasien->nama_pj }}" placeholder="Nama wali/keluarga">
            </div>
            <div class="col-md-4">
                <label for="add_hubungan_pj" class="form-label">Hubungan Pasien</label>
                <select class="form-select form-select-sm @error('hubungan_pj') is-invalid @enderror"
                    id="add_hubungan_pj" name="hubungan_pj">
                    <option value="">-- Pilih Hubungan --</option>
                    @foreach (['Orang Tua', 'Suami/Istri', 'Anak', 'Saudara Kandung', 'Lainnya'] as $hbg)
                        <option value="{{ $hbg }}" {{ $pasien->hubungan_pj == $hbg ? 'selected' : '' }}>
                            {{ $hbg }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="add_no_hp_pj" class="form-label">No. HP Penanggung Jawab</label>
                <input type="text" class="form-control form-control-sm @error('no_hp_pj') is-invalid @enderror"
                    id="add_no_hp_pj" name="no_hp_pj" value="{{ $pasien->no_hp_pj }}" placeholder="08123456789">
            </div>
        </div>
    </div>
{{-- </form> --}}

<script>
    $(document).ready(function() {
        // Ambil data awal dari variabel $pasien
        let defaultProv = "{{ $pasien->provinsi_code }}";
        let defaultKab  = "{{ $pasien->kabkota_code }}";
        let defaultKec  = "{{ $pasien->kecamatan_code }}";
        let defaultKel  = "{{ $pasien->kelurahan_code }}";

        // Function loader bersarang (Chained Loaders)
        function loadKabupaten(provCode, selectedKab = null, callback = null) {
            let $kabSelect = $('#add_kabkota_code_1');
            let $kecSelect = $('#add_kecamatan_code_1');
            let $kelSelect = $('#add_kelurahan_code_1');

            $kabSelect.html('<option value="">-- Pilih Kab/Kota --</option>').prop('disabled', true);
            $kecSelect.html('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
            $kelSelect.html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);

            if (provCode) {
                $.ajax({
                    url: "{{ route('getKabKotaByProv') }}",
                    type: "GET",
                    data: { provinsi_code: provCode },
                    success: function(response) {
                        $.each(response, function(index, item) {
                            let isSelected = (selectedKab && item.code == selectedKab) ? 'selected' : '';
                            $kabSelect.append('<option value="' + item.code + '" ' + isSelected + '>' + item.name + '</option>');
                        });
                        $kabSelect.prop('disabled', false);
                        if (callback) callback();
                    }
                });
            }
        }

        function loadKecamatan(kabCode, selectedKec = null, callback = null) {
            let $kecSelect = $('#add_kecamatan_code_1');
            let $kelSelect = $('#add_kelurahan_code_1');

            $kecSelect.html('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
            $kelSelect.html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);

            if (kabCode) {
                $.ajax({
                    url: "{{ route('getKecamatanByKab') }}",
                    type: "GET",
                    data: { kabkota_code: kabCode },
                    success: function(response) {
                        $.each(response, function(index, item) {
                            let isSelected = (selectedKec && item.code == selectedKec) ? 'selected' : '';
                            $kecSelect.append('<option value="' + item.code + '" ' + isSelected + '>' + item.name + '</option>');
                        });
                        $kecSelect.prop('disabled', false);
                        if (callback) callback();
                    }
                });
            }
        }

        function loadKelurahan(kecCode, selectedKel = null) {
            let $kelSelect = $('#add_kelurahan_code_1');
            $kelSelect.html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);

            if (kecCode) {
                $.ajax({
                    url: "{{ route('getKelurahanByKec') }}",
                    type: "GET",
                    data: { kecamatan_code: kecCode },
                    success: function(response) {
                        $.each(response, function(index, item) {
                            let isSelected = (selectedKel && item.code == selectedKel) ? 'selected' : '';
                            $kelSelect.append('<option value="' + item.code + '" ' + isSelected + '>' + item.name + '</option>');
                        });
                        $kelSelect.prop('disabled', false);
                    }
                });
            }
        }

        // --- 1. OTOMATIS LOAD SAAT PERTAMA KALI HALAMAN MODAL TERBUKA ---
        if (defaultProv) {
            loadKabupaten(defaultProv, defaultKab, function() {
                if (defaultKab) {
                    loadKecamatan(defaultKab, defaultKec, function() {
                        if (defaultKec) {
                            loadKelurahan(defaultKec, defaultKel);
                        }
                    });
                }
            });
        }

        // --- 2. EVENT LISTENER UNTUK PERUBAHAN MANUAL OLEH USER ---
        $('#add_provinsi_code_1').on('change', function() {
            loadKabupaten($(this).val());
        });

        $('#add_kabkota_code_1').on('change', function() {
            loadKecamatan($(this).val());
        });

        $('#add_kecamatan_code_1').on('change', function() {
            loadKelurahan($(this).val());
        });
    });
</script>