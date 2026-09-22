@extends('Template.Main')

@section('container')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Kasir & Pembayaran</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Kasir</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <!-- Ringkasan Singkat -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-warning text-dark mb-2">
                        <div class="card-body p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block small text-uppercase fw-bold">Belum Dibayar (In-Progress)</span>
                                <h3 class="mb-0 fw-bold">{{ $totalInProgress ?? 0 }} Pasien</h3>
                            </div>
                            <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white mb-2">
                        <div class="card-body p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block small text-uppercase fw-bold">Selesai Hari Ini</span>
                                <h3 class="mb-0 fw-bold">{{ $totalLunas ?? 0 }} Pasien</h3>
                            </div>
                            <i class="bi bi-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white mb-2">
                        <div class="card-body p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block small text-uppercase fw-bold">Total Pendapatan Hari Ini</span>
                                <h3 class="mb-0 fw-bold">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</h3>
                            </div>
                            <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Table Card -->
            <div class="card">
                <div class="card-header bg-white py-3">
                    <form action="" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tanggal Kunjungan</label>
                            <input type="date" name="tanggal" class="form-control form-control-sm"
                                value="{{ request('tanggal', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Status Pembayaran</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="BELUM LUNAS"
                                    {{ request('status', 'BELUM LUNAS') == 'BELUM LUNAS' ? 'selected' : '' }}>Belum
                                    dibayar (Menunggu Pembayaran)</option>
                                <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                                <option value="semua" {{ request('status') == 'semua' ? 'selected' : '' }}>Semua Status
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Cari Pasien / No. RM / No. Reg</label>
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Ketik Nama / No. RM..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-filter me-1"></i> Filter
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-light btn-sm border" title="Reset">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>No. Transaksi</th>
                                    <th>No. Registrasi</th>
                                    <th>No. RM</th>
                                    <th>Nama Pasien</th>
                                    <th>Poli / Unit</th>
                                    <th>Penjamin</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($listKunjungan ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $listKunjungan->firstItem() + $index }}</td>
                                        <td><span
                                                class="fw-bold font-monospace text-primary">{{ $item->no_transaksi }}</span>
                                        </td>
                                        <td><span
                                                class="fw-bold font-monospace">{{ $item->kunjungan->no_registrasi ?? '-' }}</span>
                                        </td>
                                        <td>{{ $item->kunjungan->pasien->no_rm ?? '-' }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $item->kunjungan->pasien->nama_lengkap ?? '-' }}</div>
                                            <small class="text-muted">
                                                {{ ($item->kunjungan->pasien->jenis_kelamin ?? '') == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                            </small>
                                        </td>
                                        <td>{{ $item->kunjungan->poli->nama_lokasi ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-outline-secondary border text-dark">
                                                {{ strtoupper($item->kunjungan->penjamin ?? 'UMUM') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if (strtoupper($item->status_tagihan) === 'BELUM LUNAS')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-hourglass-split me-1"></i> Belum Lunas
                                                </span>
                                            @elseif(strtoupper($item->status_tagihan) === 'LUNAS')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i> Lunas
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle me-1"></i> {{ $item->status_tagihan }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-primary px-3 btn-detail-tagihan"
                                                data-id="{{ $item->id }}"
                                                data-kunjungan-id="{{ $item->kunjungan_id }}" data-bs-toggle="modal"
                                                data-bs-target="#modalDetailTagihan">
                                                <i class="bi bi-receipt me-1"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                            Tidak ada data pembayaran dengan status terpilih pada tanggal ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if (isset($listKunjungan) && method_exists($listKunjungan, 'links'))
                    <div class="card-footer bg-white">
                        {{ $listKunjungan->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Detail Tagihan -->
    <div class="modal fade" id="modalDetailTagihan" tabindex="-1" aria-labelledby="modalDetailTagihanLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalDetailTagihanLabel">
                        <i class="bi bi-calculator me-2"></i>Rincian Tagihan Pasien
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Info Pasien & Status Pembayaran -->
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-5">
                                <small class="text-muted d-block">Nama Pasien / RM:</small>
                                <span class="fw-bold" id="detail-nama-pasien">-</span> (<span id="detail-no-rm">-</span>)
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">No. Registrasi / Poli:</small>
                                <span class="fw-bold" id="detail-no-reg">-</span> / <span id="detail-poli">-</span>
                            </div>
                            <div class="col-md-3 text-md-end">
                                <small class="text-muted d-block">Status Tagihan:</small>
                                <span id="detail-status-pembayaran" class="badge bg-secondary fs-6">MEMUAT...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Alert Informasi Jika Sudah Lunas -->
                    <div id="alert-pembayaran-lunas" class="alert alert-success d-none mb-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>Tagihan ini sudah <strong>LUNAS</strong>.
                        <span id="detail-info-transaksi" class="d-block small mt-1"></span>
                    </div>

                    <!-- Header Rincian & Tombol Tambah (Disembunyikan jika LUNAS) -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-list-check me-1"></i> Item Tindakan & Obat</h6>
                        <div class="btn-group btn-group-sm section-tambah-item">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#modalTambahTindakan">
                                <i class="bi bi-plus-circle me-1"></i> Tindakan
                            </button>
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal"
                                data-bs-target="#modalTambahObat">
                                <i class="bi bi-plus-circle me-1"></i> Obat / Alkes
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Rincian Biaya -->
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Item / Layanan</th>
                                    <th class="text-center" style="width: 80px;">Qty</th>
                                    <th class="text-end" style="width: 130px;">Harga (Rp)</th>
                                    <th class="text-end" style="width: 140px;">Subtotal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-rincian-tagihan">
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Memuat data rincian...</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold bg-light">
                                    <td colspan="3" class="text-end">Total Tagihan:</td>
                                    <td class="text-end text-primary fs-6" id="detail-total-tagihan">Rp 0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Form Pembayaran -->
                    <form id="form-pembayaran" action="#" method="POST">
                        @csrf
                        <input type="hidden" name="kunjungan_id" id="detail-kunjungan-id">
                        <div class="row g-2 border-top pt-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Metode Pembayaran</label>
                                <select name="metode_pembayaran" id="select-metode-pembayaran"
                                    class="form-select form-select-sm" required>
                                    <option value="tunai">Tunai</option>
                                    <option value="qris">QRIS / E-Wallet</option>
                                    <option value="debit">Kartu Debit / Kredit</option>
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="bpjs">Jaminan BPJS</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Jumlah Dibayar (Rp)</label>
                                <input type="number" name="jumlah_bayar" id="input-jumlah-bayar"
                                    class="form-control form-control-sm" placeholder="Masukkan nominal..." required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-success btn-sm" id="btn-proses-pembayaran">
                        <i class="bi bi-printer me-1"></i> Bayar & Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Tindakan -->
    <div class="modal fade" id="modalTambahTindakan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title"><i class="bi bi-plus-lg me-1"></i> Tambah Tindakan / Layanan</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="form-tambah-tindakan">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Pilih Tindakan</label>
                            <select class="form-select form-select-sm" id="select-tindakan-id" name="tindakan_id"
                                required>
                                <option value="">-- Pilih Tindakan --</option>
                            </select>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Jumlah (Qty)</label>
                                <input type="number" class="form-control form-control-sm" name="qty"
                                    id="qty-tindakan" value="1" min="1" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Harga (Rp)</label>
                                <input type="number" class="form-control form-control-sm" name="harga"
                                    id="harga-tindakan" required readonly>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalDetailTagihan">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>
                            Tambahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Obat -->
    <div class="modal fade" id="modalTambahObat" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-2">
                    <h6 class="modal-title"><i class="bi bi-plus-lg me-1"></i> Tambah Obat / Alkes</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="form-tambah-obat">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Pilih Obat / Alkes</label>
                            <select class="form-select form-select-sm" id="select-obat-id" name="obat_id" required>
                                <option value="">-- Pilih Obat --</option>
                            </select>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold">Jumlah (Qty)</label>
                                <input type="number" class="form-control form-control-sm" name="qty" id="qty-obat"
                                    value="1" min="1" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">Harga Satuan (Rp)</label>
                                <input type="number" class="form-control form-control-sm" name="harga"
                                    id="harga-obat" required readonly>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalDetailTagihan">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check-lg me-1"></i>
                            Tambahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const detailButtons = document.querySelectorAll('.btn-detail-tagihan');
            const tbodyRincian = document.getElementById('tbody-rincian-tagihan');
            const inputJumlahBayar = document.getElementById('input-jumlah-bayar');
            const selectMetodePembayaran = document.getElementById('select-metode-pembayaran');
            const formPembayaran = document.getElementById('form-pembayaran');
            const btnProsesPembayaran = document.getElementById('btn-proses-pembayaran');
            const statusBadge = document.getElementById('detail-status-pembayaran');
            const alertLunas = document.getElementById('alert-pembayaran-lunas');
            const infoTransaksi = document.getElementById('detail-info-transaksi');
            const sectionTambahItem = document.querySelectorAll('.section-tambah-item');

            let currentTagihanId = null;
            let currentKunjunganId = null;

            // Load Master Data Tindakan & Obat untuk Select Option
            function loadMasterData() {
                fetch(`{{ url('/master/tindakan/list') }}`)
                    .then(res => res.json())
                    .then(data => {
                        let options = '<option value="">-- Pilih Tindakan --</option>';
                        data.forEach(item => {
                            options +=
                                `<option value="${item.id}" data-harga="${item.harga}">${item.nama_tindakan} - Rp ${Number(item.harga).toLocaleString('id-ID')}</option>`;
                        });
                        document.getElementById('select-tindakan-id').innerHTML = options;
                    });

                fetch(`{{ url('/master/obat/list') }}`)
                    .then(res => res.json())
                    .then(data => {
                        let options = '<option value="">-- Pilih Obat --</option>';
                        data.forEach(item => {
                            options +=
                                `<option value="${item.id}" data-harga="${item.harga}">${item.nama_obat} - Rp ${Number(item.harga).toLocaleString('id-ID')}</option>`;
                        });
                        document.getElementById('select-obat-id').innerHTML = options;
                    });
            }
            loadMasterData();

            // Set otomatis harga saat item dipilih
            document.getElementById('select-tindakan-id').addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                document.getElementById('harga-tindakan').value = selected.getAttribute('data-harga') || 0;
            });

            document.getElementById('select-obat-id').addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                document.getElementById('harga-obat').value = selected.getAttribute('data-harga') || 0;
            });

            // Fetch Detail Tagihan
            function loadDetailTagihan(id, kunjunganId) {
                tbodyRincian.innerHTML =
                    `<tr><td colspan="4" class="text-center text-muted"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat data rincian...</td></tr>`;

                fetch(`{{ url('/kasir/detail') }}/${id}/${kunjunganId}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Gagal mengambil data dari server.');
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('detail-nama-pasien').innerText = data.pasien.nama;
                        document.getElementById('detail-no-rm').innerText = data.pasien.no_rm;
                        document.getElementById('detail-no-reg').innerText = data.no_registrasi;
                        document.getElementById('detail-poli').innerText = data.poli.nama_poli;

                        if (data.is_lunas) {
                            statusBadge.className = 'badge bg-success fs-6';
                            statusBadge.innerText = 'LUNAS';
                            alertLunas.classList.remove('d-none');
                            sectionTambahItem.forEach(el => el.classList.add('d-none'));

                            if (data.detail_pembayaran) {
                                const tgl = data.detail_pembayaran.tanggal_bayar ? new Date(
                                    data.detail_pembayaran.tanggal_bayar).toLocaleString('id-ID') : '-';
                                infoTransaksi.innerText =
                                    `Metode: ${data.detail_pembayaran.metode_pembayaran.toUpperCase()} | Nominal: Rp ${Number(data.detail_pembayaran.jumlah_bayar).toLocaleString('id-ID')} | Waktu: ${tgl}`;
                            }

                            inputJumlahBayar.disabled = true;
                            selectMetodePembayaran.disabled = true;
                            btnProsesPembayaran.disabled = true;
                        } else {
                            statusBadge.className = 'badge bg-danger fs-6';
                            statusBadge.innerText = 'BELUM DIBAYAR';
                            alertLunas.classList.add('d-none');
                            sectionTambahItem.forEach(el => el.classList.remove('d-none'));

                            inputJumlahBayar.disabled = false;
                            selectMetodePembayaran.disabled = false;
                            btnProsesPembayaran.disabled = false;
                        }

                        let htmlRows = '';
                        if (data.rincian && data.rincian.length > 0) {
                            data.rincian.forEach(item => {
                                htmlRows += `
                                <tr>
                                    <td>${item.nama_layanan}</td>
                                    <td class="text-center">${item.qty}</td>
                                    <td class="text-end">Rp ${Number(item.harga).toLocaleString('id-ID')}</td>
                                    <td class="text-end">Rp ${Number(item.subtotal).toLocaleString('id-ID')}</td>
                                </tr>`;
                            });
                        } else {
                            htmlRows =
                                `<tr><td colspan="4" class="text-center text-muted">Belum ada rincian tindakan atau resep obat.</td></tr>`;
                        }

                        tbodyRincian.innerHTML = htmlRows;
                        document.getElementById('detail-total-tagihan').innerText = 'Rp ' + Number(data.total)
                            .toLocaleString('id-ID');

                        if (!data.is_lunas) {
                            inputJumlahBayar.value = data.total;
                        } else if (data.detail_pembayaran) {
                            inputJumlahBayar.value = data.detail_pembayaran.jumlah_bayar;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        tbodyRincian.innerHTML =
                            `<tr><td colspan="4" class="text-center text-danger">Gagal memuat rincian tagihan.</td></tr>`;
                    });
            }

            // Event tombol 'Detail Tagihan'
            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    currentTagihanId = this.getAttribute('data-id');
                    currentKunjunganId = this.getAttribute('data-kunjungan-id');

                    document.getElementById('detail-kunjungan-id').value = currentTagihanId;
                    formPembayaran.action = "{{ route('kasir.bayar') }}";

                    loadDetailTagihan(currentTagihanId, currentKunjunganId);
                });
            });

            // Submit Tambah Tindakan via AJAX
            document.getElementById('form-tambah-tindakan').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('tagihan_id', currentTagihanId);
                formData.append('kunjungan_id', currentKunjunganId);

                fetch("{{ route('kasir.tambah-tindakan') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'modalTambahTindakan'));
                            modal.hide();
                            loadDetailTagihan(currentTagihanId, currentKunjunganId);
                            const modalDetail = new bootstrap.Modal(document.getElementById(
                                'modalDetailTagihan'));
                            modalDetail.show();
                        } else {
                            alert(res.message || 'Gagal menambahkan tindakan.');
                        }
                    });
            });

            // Submit Tambah Obat via AJAX
            document.getElementById('form-tambah-obat').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('tagihan_id', currentTagihanId);
                formData.append('kunjungan_id', currentKunjunganId);

                fetch("{{ route('kasir.tambah-obat') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'modalTambahObat'));
                            modal.hide();
                            loadDetailTagihan(currentTagihanId, currentKunjunganId);
                            const modalDetail = new bootstrap.Modal(document.getElementById(
                                'modalDetailTagihan'));
                            modalDetail.show();
                        } else {
                            alert(res.message || 'Gagal menambahkan obat.');
                        }
                    });
            });

            // Trigger Submit Form Pembayaran
            btnProsesPembayaran.addEventListener('click', function() {
                if (formPembayaran.checkValidity()) {
                    formPembayaran.submit();
                } else {
                    formPembayaran.reportValidity();
                }
            });
        });
    </script>
@endsection
