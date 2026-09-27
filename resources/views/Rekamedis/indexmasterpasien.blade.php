@extends('Template.Main')
@section('container')
<div class="v_1">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master Pasien</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Master Pasien</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <!-- Card Download Data Wilayah SATUSEHAT -->
            <div class="card card-outline card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-geo-alt-fill me-1"></i> Master Wilayah SATUSEHAT
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-secondary small mb-3">
                        Klik tombol di bawah untuk mengambil atau mengunduh data master wilayah dari API SATUSEHAT Kemenkes:
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <!-- 1. Get Data Provinsi (Langsung) -->
                        <a href="{{ route('getprov') }}" target="_blank" class="btn btn-primary btn-sm">
                            <i class="bi bi-cloud-arrow-down me-1"></i> Get Data Provinsi
                        </a>

                        <!-- 2. Get Data Kab/Kota (Panggil Modal) -->
                        <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal"
                            data-bs-target="#modalKabKota">
                            <i class="bi bi-cloud-arrow-down me-1"></i> Get Data Kab/Kota
                        </button>

                        <button type="button" class="btn btn-warning btn-sm text-white" data-bs-toggle="modal"
                            data-bs-target="#modalKecamatan">
                            <i class="bi bi-cloud-arrow-down me-1"></i> Get Data Kecamatan
                        </button>

                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalKelurahan">
                            <i class="bi bi-cloud-arrow-down me-1"></i> Get Data Kelurahan
                        </button>
                    </div>
                </div>
            </div>
            @if ($errors->any())
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var modalEl = document.getElementById('modalTambahPasien');
                        if (modalEl) {
                            var myModal = new bootstrap.Modal(modalEl);
                            myModal.show();
                        }
                    });
                </script>
            @endif
            <!-- Card Filter / Pencarian Pasien & Tombol Tambah Pasien -->
            <div class="card card-outline card-info mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-search me-1"></i> Pencarian Data Pasien
                    </h3>
                    <!-- Tombol Tambah Pasien Baru -->
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalTambahPasien">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Pasien Baru
                    </button>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form id="formFilterPasien">
                        <div class="row g-3">
                            <!-- No. RM -->
                            <div class="col-md-3">
                                <label for="no_rm" class="form-label fw-bold">No. RM</label>
                                <input type="text" class="form-control form-control-sm" id="no_rm" name="no_rm"
                                    placeholder="Contoh: 00-12-34-56">
                            </div>

                            <!-- No. KTP / NIK -->
                            <div class="col-md-3">
                                <label for="no_ktp" class="form-label fw-bold">No. KTP / NIK</label>
                                <input type="text" class="form-control form-control-sm" id="no_ktp" name="no_ktp"
                                    placeholder="16 Digit NIK">
                            </div>

                            <!-- Nama Pasien -->
                            <div class="col-md-3">
                                <label for="nama" class="form-label fw-bold">Nama Pasien</label>
                                <input type="text" class="form-control form-control-sm" id="nama" name="nama"
                                    placeholder="Nama lengkap pasien">
                            </div>

                            <!-- Alamat -->
                            <div class="col-md-3">
                                <label for="alamat" class="form-label fw-bold">Alamat Pasien</label>
                                <input type="text" class="form-control form-control-sm" id="alamat" name="alamat"
                                    placeholder="Jalan, Kelurahan, Kecamatan">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="button" id="btnReset" class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </button>
                            <button type="submit" id="btnCari" class="btn btn-info btn-sm text-white">
                                <i class="bi bi-search me-1"></i> Cari Pasien
                            </button>
                        </div>
                    </form>

                    <!-- Container Tabel Pasien -->
                    <div class="v_tabel_pasien mt-4"></div>
                </div>
            </div>

            <!-- Tempat Tabel Data Pasien Utama Anda -->

        </div>
    </div>
</div>
<div hidden class="v_2">
    <div class="app-content">
        <div class="v_formnya">

        </div>
    </div>
</div>
    <div class="modal fade" id="modalTambahPasien" tabindex="-1" aria-labelledby="modalTambahPasienLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form action="{{ route('pasien.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalTambahPasienLabel">
                            <i class="bi bi-person-plus-fill me-1"></i> Form Registrasi Pasien Baru
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <!-- Alert General Error (jika ada exception dari controller) -->
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Section 1: Identitas SIMRS, SATUSEHAT & BPJS -->
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-card-heading me-1"></i> Identitas Sistem & Integrasi
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="add_no_rm" class="form-label fw-bold">No. RM</label>
                                <input type="text"
                                    class="form-control form-control-sm @error('no_rm') is-invalid @enderror"
                                    id="add_no_rm" name="no_rm" value="{{ old('no_rm') }}" placeholder="00-00-00-01">
                                @error('no_rm')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="add_nik" class="form-label fw-bold">NIK / No. KTP <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control form-control-sm @error('nik') is-invalid @enderror" id="add_nik"
                                    name="nik" value="{{ old('nik') }}" maxlength="16" required
                                    placeholder="16 Digit NIK">
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="add_no_bpjs" class="form-label">No. Kartu BPJS</label>
                                <input type="text"
                                    class="form-control form-control-sm @error('no_bpjs') is-invalid @enderror"
                                    id="add_no_bpjs" name="no_bpjs" value="{{ old('no_bpjs') }}" maxlength="13"
                                    placeholder="13 Digit No. BPJS">
                                @error('no_bpjs')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="add_ihs_number" class="form-label">IHS Number SATUSEHAT</label>
                                <input type="text"
                                    class="form-control form-control-sm @error('ihs_number') is-invalid @enderror"
                                    id="add_ihs_number" name="ihs_number" value="{{ old('ihs_number') }}"
                                    placeholder="Otomatis / Manual">
                                @error('ihs_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Section 2: Data Diri Utama -->
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-person-vcard me-1"></i> Data Diri Pasien
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-2">
                                <label for="add_gelar_depan" class="form-label">Gelar Depan</label>
                                <input type="text"
                                    class="form-control form-control-sm @error('gelar_depan') is-invalid @enderror"
                                    id="add_gelar_depan" name="gelar_depan" value="{{ old('gelar_depan') }}"
                                    placeholder="dr. / H.">
                                @error('gelar_depan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="add_nama_lengkap" class="form-label fw-bold">Nama Lengkap <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control form-control-sm @error('nama_lengkap') is-invalid @enderror"
                                    id="add_nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                                    placeholder="Nama lengkap sesuai KTP">
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="add_gelar_belakang" class="form-label">Gelar Belakang</label>
                                <input type="text"
                                    class="form-control form-control-sm @error('gelar_belakang') is-invalid @enderror"
                                    id="add_gelar_belakang" name="gelar_belakang" value="{{ old('gelar_belakang') }}"
                                    placeholder="S.Kom. / M.Kes.">
                                @error('gelar_belakang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="add_jenis_kelamin" class="form-label fw-bold">Jenis Kelamin <span
                                        class="text-danger">*</span></label>
                                <select class="form-select form-select-sm @error('jenis_kelamin') is-invalid @enderror"
                                    id="add_jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki
                                    </option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan
                                    </option>
                                </select>
                                @error('jenis_kelamin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="add_tempat_lahir" class="form-label fw-bold">Tempat Lahir <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control form-control-sm @error('tempat_lahir') is-invalid @enderror"
                                    id="add_tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required
                                    placeholder="Kota lahir">
                                @error('tempat_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="add_tanggal_lahir" class="form-label fw-bold">Tanggal Lahir <span
                                        class="text-danger">*</span></label>
                                <input type="date"
                                    class="form-control form-control-sm @error('tanggal_lahir') is-invalid @enderror"
                                    id="add_tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                    required>
                                @error('tanggal_lahir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-1">
                                <label for="add_golongan_darah" class="form-label">Gol. Darah</label>
                                <select class="form-select form-select-sm @error('golongan_darah') is-invalid @enderror"
                                    id="add_golongan_darah" name="golongan_darah">
                                    <option value="">-</option>
                                    <option value="A" {{ old('golongan_darah') == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ old('golongan_darah') == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="AB" {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}>AB
                                    </option>
                                    <option value="O" {{ old('golongan_darah') == 'O' ? 'selected' : '' }}>O</option>
                                </select>
                                @error('golongan_darah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-2">
                                <label for="add_rhesus" class="form-label">Rhesus</label>
                                <select class="form-select form-select-sm @error('rhesus') is-invalid @enderror"
                                    id="add_rhesus" name="rhesus">
                                    <option value="">-</option>
                                    <option value="+" {{ old('rhesus') == '+' ? 'selected' : '' }}>Positif (+)
                                    </option>
                                    <option value="-" {{ old('rhesus') == '-' ? 'selected' : '' }}>Negatif (-)
                                    </option>
                                </select>
                                @error('rhesus')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Section 3: Profil Demografi & Kontak -->
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-person-lines-fill me-1"></i> Demografi & Kontak
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="add_agama" class="form-label">Agama</label>
                                <select class="form-select form-select-sm @error('agama') is-invalid @enderror"
                                    id="add_agama" name="agama">
                                    <option value="">-- Pilih Agama --</option>
                                    @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                                        <option value="{{ $agm }}" {{ old('agama') == $agm ? 'selected' : '' }}>
                                            {{ $agm }}</option>
                                    @endforeach
                                </select>
                                @error('agama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="add_status_pernikahan" class="form-label">Status Pernikahan</label>
                                <select
                                    class="form-select form-select-sm @error('status_pernikahan') is-invalid @enderror"
                                    id="add_status_pernikahan" name="status_pernikahan">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="S" {{ old('status_pernikahan') == 'S' ? 'selected' : '' }}>Belum
                                        Menikah (Single)</option>
                                    <option value="M" {{ old('status_pernikahan') == 'M' ? 'selected' : '' }}>Menikah
                                        (Married)</option>
                                    <option value="D" {{ old('status_pernikahan') == 'D' ? 'selected' : '' }}>Cerai
                                        Hidup (Divorced)</option>
                                    <option value="W" {{ old('status_pernikahan') == 'W' ? 'selected' : '' }}>Cerai
                                        Mati (Widowed)</option>
                                </select>
                                @error('status_pernikahan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="add_pekerjaan" class="form-label">Pekerjaan</label>
                                <input type="text"
                                    class="form-control form-control-sm @error('pekerjaan') is-invalid @enderror"
                                    id="add_pekerjaan" name="pekerjaan" value="{{ old('pekerjaan') }}"
                                    placeholder="PNS/Swasta/Wiraswasta">
                                @error('pekerjaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="add_pendidikan" class="form-label">Pendidikan</label>
                                <select class="form-select form-select-sm @error('pendidikan') is-invalid @enderror"
                                    id="add_pendidikan" name="pendidikan">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    @foreach (['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'] as $pdk)
                                        <option value="{{ $pdk }}"
                                            {{ old('pendidikan') == $pdk ? 'selected' : '' }}>{{ $pdk }}</option>
                                    @endforeach
                                </select>
                                @error('pendidikan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="add_kewarganegaraan" class="form-label">Kewarganegaraan</label>
                                <select class="form-select form-select-sm @error('kewarganegaraan') is-invalid @enderror"
                                    id="add_kewarganegaraan" name="kewarganegaraan">
                                    <option value="WNI" {{ old('kewarganegaraan', 'WNI') == 'WNI' ? 'selected' : '' }}>
                                        WNI</option>
                                    <option value="WNA" {{ old('kewarganegaraan') == 'WNA' ? 'selected' : '' }}>WNA
                                    </option>
                                </select>
                                @error('kewarganegaraan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="add_no_hp" class="form-label">No. HP / Whatsapp</label>
                                <input type="text"
                                    class="form-control form-control-sm @error('no_hp') is-invalid @enderror"
                                    id="add_no_hp" name="no_hp" value="{{ old('no_hp') }}"
                                    placeholder="08123456789">
                                @error('no_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="add_email" class="form-label">Email Pasien</label>
                                <input type="email"
                                    class="form-control form-control-sm @error('email') is-invalid @enderror"
                                    id="add_email" name="email" value="{{ old('email') }}"
                                    placeholder="contoh@email.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Section 4: Alamat Domisili -->
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-geo-alt me-1"></i> Alamat Domisili
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="add_alamat" class="form-label fw-bold">Alamat Lengkap <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control form-control-sm @error('alamat') is-invalid @enderror" id="add_alamat" name="alamat"
                                    rows="3" required placeholder="Jalan, No. Rumah, Blok, Gang">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label for="add_rt" class="form-label">RT</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('rt') is-invalid @enderror"
                                            id="add_rt" name="rt" value="{{ old('rt') }}"
                                            placeholder="001">
                                        @error('rt')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="add_rw" class="form-label">RW</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('rw') is-invalid @enderror"
                                            id="add_rw" name="rw" value="{{ old('rw') }}"
                                            placeholder="002">
                                        @error('rw')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="add_kode_pos" class="form-label">Kode Pos</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('kode_pos') is-invalid @enderror"
                                            id="add_kode_pos" name="kode_pos" value="{{ old('kode_pos') }}"
                                            placeholder="45188">
                                        @error('kode_pos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="add_provinsi_code" class="form-label">Provinsi</label>
                                <select class="form-select form-select-sm @error('provinsi_code') is-invalid @enderror"
                                    id="add_provinsi_code" name="provinsi_code">
                                    <option value="">-- Pilih Provinsi --</option>
                                    @foreach ($listProvinsi as $prov)
                                        <option value="{{ $prov->code }}"
                                            {{ old('provinsi_code') == $prov->code ? 'selected' : '' }}>
                                            {{ $prov->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('provinsi_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="add_kabkota_code" class="form-label">Kabupaten / Kota</label>
                                <select class="form-select form-select-sm @error('kabkota_code') is-invalid @enderror"
                                    id="add_kabkota_code" name="kabkota_code" disabled>
                                    <option value="">-- Pilih Kab/Kota --</option>
                                </select>
                                @error('kabkota_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="add_kecamatan_code" class="form-label">Kecamatan</label>
                                <select class="form-select form-select-sm @error('kecamatan_code') is-invalid @enderror"
                                    id="add_kecamatan_code" name="kecamatan_code" disabled>
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                @error('kecamatan_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="add_kelurahan_code" class="form-label">Kelurahan / Desa</label>
                                <select class="form-select form-select-sm @error('kelurahan_code') is-invalid @enderror"
                                    id="add_kelurahan_code" name="kelurahan_code" disabled>
                                    <option value="">-- Pilih Kelurahan --</option>
                                </select>
                                @error('kelurahan_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Section 5: Penanggung Jawab -->
                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-telephone-outbound me-1"></i> Penanggung Jawab / Kontak Darurat
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="add_nama_pj" class="form-label">Nama Penanggung Jawab</label>
                                <input type="text"
                                    class="form-control form-control-sm @error('nama_pj') is-invalid @enderror"
                                    id="add_nama_pj" name="nama_pj" value="{{ old('nama_pj') }}"
                                    placeholder="Nama wali/keluarga">
                                @error('nama_pj')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="add_hubungan_pj" class="form-label">Hubungan Pasien</label>
                                <select class="form-select form-select-sm @error('hubungan_pj') is-invalid @enderror"
                                    id="add_hubungan_pj" name="hubungan_pj">
                                    <option value="">-- Pilih Hubungan --</option>
                                    @foreach (['Orang Tua', 'Suami/Istri', 'Anak', 'Saudara Kandung', 'Lainnya'] as $hbg)
                                        <option value="{{ $hbg }}"
                                            {{ old('hubungan_pj') == $hbg ? 'selected' : '' }}>{{ $hbg }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('hubungan_pj')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="add_no_hp_pj" class="form-label">No. HP Penanggung Jawab</label>
                                <input type="text"
                                    class="form-control form-control-sm @error('no_hp_pj') is-invalid @enderror"
                                    id="add_no_hp_pj" name="no_hp_pj" value="{{ old('no_hp_pj') }}"
                                    placeholder="08123456789">
                                @error('no_hp_pj')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-save me-1"></i> Simpan Data Pasien
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Pilih Provinsi untuk Kab/Kota -->
    <div class="modal fade" id="modalKabKota" tabindex="-1" aria-labelledby="modalKabKotaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('getKabKota') }}" method="GET" target="_blank">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalKabKotaLabel">Pilih Provinsi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="code_prov" class="form-label">Provinsi Target</label>
                            <select name="codes_prov" id="code_prov" class="form-select" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach ($listProvinsi as $prov)
                                    <option value="{{ $prov->code }}">{{ $prov->name }} ({{ $prov->code }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Pilih provinsi yang akan ditarik data Kab/Kota-nya dari SATUSEHAT.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info btn-sm text-white">
                            <i class="bi bi-cloud-arrow-down me-1"></i> Tarik Data Kab/Kota
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Get Data Kecamatan -->
    <div class="modal fade" id="modalKecamatan" tabindex="-1" aria-labelledby="modalKecamatanLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('getKec') }}" method="GET" target="_blank">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalKecamatanLabel">Pilih Wilayah (Kecamatan)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Dropdown Provinsi -->
                        <div class="mb-3">
                            <label for="provinsi_for_kec" class="form-label">1. Pilih Provinsi</label>
                            <select id="provinsi_for_kec" class="form-select" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach ($listProvinsi as $prov)
                                    <option value="{{ $prov->code }}">{{ $prov->name }} ({{ $prov->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dropdown Kab/Kota (Dinamis via AJAX) -->
                        <div class="mb-3">
                            <label for="kabkota_for_kec" class="form-label">2. Pilih Kab/Kota</label>
                            <select name="codes_kabkota" id="kabkota_for_kec" class="form-select" required disabled>
                                <option value="">-- Pilih Provinsi Terlebih Dahulu --</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning btn-sm text-white">
                            <i class="bi bi-cloud-arrow-down me-1"></i> Tarik Data Kecamatan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Get Data Kelurahan -->
    <div class="modal fade" id="modalKelurahan" tabindex="-1" aria-labelledby="modalKelurahanLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('getKelurahan') }}" method="GET" target="_blank">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalKelurahanLabel">Pilih Wilayah (Kelurahan/Desa)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Dropdown 1: Provinsi -->
                        <div class="mb-3">
                            <label for="provinsi_for_kel" class="form-label">1. Pilih Provinsi</label>
                            <select id="provinsi_for_kel" class="form-select" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach ($listProvinsi as $prov)
                                    <option value="{{ $prov->code }}">{{ $prov->name }} ({{ $prov->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dropdown 2: Kab/Kota (AJAX) -->
                        <div class="mb-3">
                            <label for="kabkota_for_kel" class="form-label">2. Pilih Kab/Kota</label>
                            <select id="kabkota_for_kel" class="form-select" required disabled>
                                <option value="">-- Pilih Provinsi Terlebih Dahulu --</option>
                            </select>
                        </div>

                        <!-- Dropdown 3: Kecamatan (AJAX) -->
                        <div class="mb-3">
                            <label for="kecamatan_for_kel" class="form-label">3. Pilih Kecamatan</label>
                            <select name="codes_kec" id="kecamatan_for_kel" class="form-select" required disabled>
                                <option value="">-- Pilih Kab/Kota Terlebih Dahulu --</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-cloud-arrow-down me-1"></i> Tarik Data Kelurahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- AJAX Cascading Wilayah untuk Form Modal Tambah Pasien -->
    <script>
        function kembali()
        {
            $('.v_1').removeAttr('hidden',true)
            $('.v_2').attr('hidden',true)
        }
        $(document).ready(function() {
             spinneron()
            // 1. Ubah Provinsi -> Load Kab/Kota di Modal Tambah Pasien
            $('#add_provinsi_code').on('change', function() {
                let provCode = $(this).val();
                let $kabSelect = $('#add_kabkota_code');
                let $kecSelect = $('#add_kecamatan_code');
                let $kelSelect = $('#add_kelurahan_code');

                $kabSelect.html('<option value="">-- Pilih Kab/Kota --</option>').prop('disabled', true);
                $kecSelect.html('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
                $kelSelect.html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);

                if (provCode) {
                    $.ajax({
                        url: "{{ route('getKabKotaByProv') }}",
                        type: "GET",
                        data: {
                            provinsi_code: provCode
                        },
                        success: function(response) {
                            $.each(response, function(index, item) {
                                $kabSelect.append('<option value="' + item.code + '">' +
                                    item.name + '</option>');
                            });
                            $kabSelect.prop('disabled', false);
                        }
                    });
                }
            });

            // 2. Ubah Kab/Kota -> Load Kecamatan
            $('#add_kabkota_code').on('change', function() {
                let kabCode = $(this).val();
                let $kecSelect = $('#add_kecamatan_code');
                let $kelSelect = $('#add_kelurahan_code');

                $kecSelect.html('<option value="">-- Pilih Kecamatan --</option>').prop('disabled', true);
                $kelSelect.html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);

                if (kabCode) {
                    $.ajax({
                        url: "{{ route('getKecamatanByKab') }}",
                        type: "GET",
                        data: {
                            kabkota_code: kabCode
                        },
                        success: function(response) {
                            $.each(response, function(index, item) {
                                $kecSelect.append('<option value="' + item.code + '">' +
                                    item.name + '</option>');
                            });
                            $kecSelect.prop('disabled', false);
                        }
                    });
                }
            });

            // 3. Ubah Kecamatan -> Load Kelurahan (Memerlukan Route tambahan jika ada)
            $('#add_kecamatan_code').on('change', function() {
                let kecCode = $(this).val();
                let $kelSelect = $('#add_kelurahan_code');
                $kelSelect.html('<option value="">-- Pilih Kelurahan --</option>').prop('disabled', true);

                if (kecCode) {
                    $.ajax({
                        url: "{{ route('getKelurahanByKec') }}", // Sesuaikan nama route AJAX kelurahan Anda
                        type: "GET",
                        data: {
                            kecamatan_code: kecCode
                        },
                        success: function(response) {
                            $.each(response, function(index, item) {
                                $kelSelect.append('<option value="' + item.code + '">' +
                                    item.name + '</option>');
                            });
                            $kelSelect.prop('disabled', false);
                        }
                    });
                }
            });
            spinneroff()
        });
    </script>
    <!-- Script Modal Get Master Wilayah Exiting -->
    <script>
        $('#provinsi_for_kec').on('change', function() {
            let provCode = $(this).val();
            let $kabSelect = $('#kabkota_for_kec');

            if (provCode) {
                $kabSelect.html('<option value="">Loading data Kab/Kota...</option>').prop('disabled', true);
                $.ajax({
                    url: "{{ route('getKabKotaByProv') }}",
                    type: "GET",
                    data: {
                        provinsi_code: provCode
                    },
                    success: function(response) {
                        $kabSelect.html('<option value="">-- Pilih Kab/Kota --</option>');
                        $.each(response, function(index, item) {
                            $kabSelect.append('<option value="' + item.code + '">' + item.name +
                                ' (' + item.code + ')</option>');
                        });
                        $kabSelect.prop('disabled', false);
                    },
                    error: function() {
                        alert('Gagal mengambil data Kabupaten/Kota!');
                        $kabSelect.html('<option value="">-- Gagal memuat data --</option>');
                    }
                });
            } else {
                $kabSelect.html('<option value="">-- Pilih Provinsi Terlebih Dahulu --</option>').prop('disabled',
                    true);
            }
        });

        // Level 1: Ubah Provinsi -> Load Kab/Kota
        $('#provinsi_for_kel').on('change', function() {
            let provCode = $(this).val();
            let $kabSelect = $('#kabkota_for_kel');
            let $kecSelect = $('#kecamatan_for_kel');

            $kecSelect.html('<option value="">-- Pilih Kab/Kota Terlebih Dahulu --</option>').prop('disabled',
                true);

            if (provCode) {
                $kabSelect.html('<option value="">Loading data Kab/Kota...</option>').prop('disabled', true);
                $.ajax({
                    url: "{{ route('getKabKotaByProv') }}",
                    type: "GET",
                    data: {
                        provinsi_code: provCode
                    },
                    success: function(response) {
                        $kabSelect.html('<option value="">-- Pilih Kab/Kota --</option>');
                        $.each(response, function(index, item) {
                            $kabSelect.append('<option value="' + item.code + '">' + item.name +
                                ' (' + item.code + ')</option>');
                        });
                        $kabSelect.prop('disabled', false);
                    }
                });
            } else {
                $kabSelect.html('<option value="">-- Pilih Provinsi Terlebih Dahulu --</option>').prop('disabled',
                    true);
            }
        });

        // Level 2: Ubah Kab/Kota -> Load Kecamatan
        $('#kabkota_for_kel').on('change', function() {
            let kabCode = $(this).val();
            let $kecSelect = $('#kecamatan_for_kel');

            if (kabCode) {
                $kecSelect.html('<option value="">Loading data Kecamatan...</option>').prop('disabled', true);
                $.ajax({
                    url: "{{ route('getKecamatanByKab') }}",
                    type: "GET",
                    data: {
                        kabkota_code: kabCode
                    },
                    success: function(response) {
                        $kecSelect.html('<option value="">-- Pilih Kecamatan --</option>');
                        $.each(response, function(index, item) {
                            $kecSelect.append('<option value="' + item.code + '">' + item.name +
                                ' (' + item.code + ')</option>');
                        });
                        $kecSelect.prop('disabled', false);
                    }
                });
            } else {
                $kecSelect.html('<option value="">-- Pilih Kab/Kota Terlebih Dahulu --</option>').prop('disabled',
                    true);
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            spinneron()
            // Fungsi Fetch Data via AJAX
            function fetchPasienData(url = "{{ route('caripasien') }}") {
                let formData = $('#formFilterPasien').serialize();

                $('.v_tabel_pasien').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-info" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted small">Memuat data pasien...</p>
            </div>
        `);

                $.ajax({
                    url: url,
                    type: "GET",
                    data: formData,
                    success: function(response) {
                        $('.v_tabel_pasien').html(response);
                    },
                    error: function(xhr) {
                        $('.v_tabel_pasien').html(`
                    <div class="alert alert-danger text-center my-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal memuat data pasien. Silakan coba lagi.
                    </div>
                `);
                    }
                });
            }

            // 1. Load Data Otomatis Saat Halaman Pertama Kali Dibuka
            fetchPasienData();

            // 2. Submit Form / Klik Tombol Cari
            $('#formFilterPasien').on('submit', function(e) {
                e.preventDefault();
                fetchPasienData();
            });

            // 3. Reset Form & Reload Data
            $('#btnReset').on('click', function(e) {
                e.preventDefault();
                $('#formFilterPasien')[0].reset();
                fetchPasienData();
            });

            // 4. Handle Klik Pagination tanpa reload halaman
            $(document).on('click', '.v_tabel_pasien .pagination a', function(e) {
                e.preventDefault();
                let pageUrl = $(this).attr('href');
                if (pageUrl) {
                    fetchPasienData(pageUrl);
                }
            });
            spinneroff()
        });
    </script>
@endsection
