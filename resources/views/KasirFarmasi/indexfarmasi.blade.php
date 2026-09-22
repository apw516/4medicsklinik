@extends('Template.Main')

@section('container')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-primary mb-0">
                <i class="bi bi-capsule me-2"></i>Daftar Resep & Obat (Sudah Lunas)
            </h4>
        </div>

        <!-- Filter Tanggal -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('indexfarmasi') }}" method="GET" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="tanggal" class="col-form-label fw-bold">Pilih Tanggal Kunjungan:</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ $tanggal }}"
                            onchange="this.form.submit()">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('indexfarmasi') }}" class="btn btn-outline-secondary">Hari Ini</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Daftar Kunjungan Lunas -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-secondary">
                    Data Kunjungan Pasien Tanggal: <span
                        class="text-primary">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">#</th>
                                <th>No. Reg / RM</th>
                                <th>Nama Pasien</th>
                                <th>Poli</th>
                                <th>Metode Bayar</th>
                                <th class="text-center">Status Pembayaran</th>
                                <th class="text-center">Status Resep</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kunjungans as $index => $kunjungan)
                                <tr>
                                    <td class="text-center">{{ $kunjungans->firstItem() + $index }}</td>
                                    <td>
                                        <span class="fw-bold d-block">{{ $kunjungan->no_registrasi ?? '-' }}</span>
                                        <small class="text-muted">RM: {{ $kunjungan->pasien->no_rm ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $kunjungan->pasien->nama_lengkap ?? '-' }}</span>
                                    </td>
                                    <td>{{ $kunjungan->poli->nama_lokasi ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            {{ strtoupper($kunjungan->pembayaran->metode_pembayaran ?? 'Lunas') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> LUNAS</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">
                                            {{ strtoupper($kunjungan->status_penyerahan_obat) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-lihat-obat"
                                            data-id="{{ $kunjungan->id }}">
                                            <i class="bi bi-eye me-1"></i> Lihat Obat
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        Tidak ada data kunjungan yang sudah dibayar pada tanggal ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($kunjungans->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ $kunjungans->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Detail Obat -->
    <!-- Modal Detail Obat -->
    <div class="modal fade" id="modalDetailObat" tabindex="-1" aria-labelledby="modalDetailObatLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalDetailObatLabel">
                        <i class="bi bi-prescription2 me-2"></i>Rincian Obat & Resep (Sudah Dibayar)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Info Pasien -->
                    <div class="p-3 bg-light rounded border mb-3">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Nama Pasien / No. RM:</small>
                                <span class="fw-bold" id="modal-nama-pasien">-</span> (<span id="modal-no-rm">-</span>)
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">No. Registrasi / Poli:</small>
                                <span class="fw-bold" id="modal-no-reg">-</span> / <span id="modal-poli">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Obat -->
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Nama Obat / Layanan</th>
                                    <th class="text-center" style="width: 100px;">Jumlah / Qty</th>
                                    <th class="text-end" style="width: 140px;">Harga (Rp)</th>
                                    <th class="text-end" style="width: 150px;">Subtotal (Rp)</th>
                                    <th class="text-center" style="width: 120px;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-rincian-obat">
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Memuat data...</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold bg-light">
                                    <td colspan="3" class="text-end">Total Tagihan Obat:</td>
                                    <td class="text-end text-success fs-6" id="modal-total-obat">Rp 0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Modal Footer (Tombol Tambahan) -->
                <div class="modal-footer bg-light d-flex justify-content-between">
                    <div>
                        <!-- Tombol Cetak Resep -->
                        <button type="button" class="btn btn-outline-primary btn-sm me-2" id="btn-cetak-resep" disabled>
                            <i class="bi bi-printer me-1"></i> Cetak Resep
                        </button>
                        <!-- Tombol Serahkan Obat & Bridging SATUSEHAT -->
                        <button type="button" class="btn btn-success btn-sm" id="btn-serahkan-obat" disabled>
                            <i class="bi bi-box-seam me-1"></i> Serahkan Obat & Bridging SATUSEHAT
                        </button>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lihatObatButtons = document.querySelectorAll('.btn-lihat-obat');
            const modalElement = new bootstrap.Modal(document.getElementById('modalDetailObat'));
            const tbodyRincian = document.getElementById('tbody-rincian-obat');

            const btnCetakResep = document.getElementById('btn-cetak-resep');
            const btnSerahkanObat = document.getElementById('btn-serahkan-obat');
            let activeKunjunganId = null;

            lihatObatButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const kunjunganId = this.getAttribute('data-id');
                    activeKunjunganId = kunjunganId;

                    // Reset UI Modal saat loading
                    tbodyRincian.innerHTML =
                        `<tr><td colspan="5" class="text-center text-muted py-3"><div class="spinner-border spinner-border-sm text-success me-2"></div>Memuat data obat...</td></tr>`;
                    document.getElementById('modal-nama-pasien').innerText = '-';
                    document.getElementById('modal-no-rm').innerText = '-';
                    document.getElementById('modal-no-reg').innerText = '-';
                    document.getElementById('modal-poli').innerText = '-';
                    document.getElementById('modal-total-obat').innerText = 'Rp 0';

                    // Disable tombol aksi sementara
                    btnCetakResep.disabled = true;
                    btnSerahkanObat.disabled = true;

                    modalElement.show();

                    // Fetch detail obat dari server
                    fetch(`{{ url('/farmasi/detail-obat') }}/${kunjunganId}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Gagal mengambil data obat.');
                            return response.json();
                        })
                        .then(data => {
                            // Populate header info pasien
                            document.getElementById('modal-nama-pasien').innerText = data
                                .kunjungan?.pasien?.nama ?? '-';
                            document.getElementById('modal-no-rm').innerText = data.kunjungan
                                ?.pasien?.no_rm ?? '-';
                            document.getElementById('modal-no-reg').innerText = data.kunjungan
                                ?.no_registrasi ?? '-';
                            document.getElementById('modal-poli').innerText = data.kunjungan
                                ?.poli?.nama_poli ?? '-';

                            // Populate tabel obat
                            let htmlRows = '';
                            let grandTotal = 0;

                            if (data.obats && data.obats.length > 0) {
                                data.obats.forEach(item => {
                                    const subtotal = Number(item.subtotal || (item.qty *
                                        item.harga));
                                    grandTotal += subtotal;

                                    htmlRows += `
                                <tr>
                                    <td>${item.nama_layanan || item.nama_obat}</td>
                                    <td class="text-center">${item.qty}</td>
                                    <td class="text-end">Rp ${Number(item.harga).toLocaleString('id-ID')}</td>
                                    <td class="text-end">Rp ${subtotal.toLocaleString('id-ID')}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">LUNAS</span>
                                    </td>
                                </tr>
                            `;
                                });

                                // Enable tombol jika ada data obat
                                btnCetakResep.disabled = false;

                                // Cek jika obat sudah pernah diserahkan / dikirim ke SATUSEHAT
                                if (data.kunjungan?.status_penyerahan_obat === 'diserahkan') {
                                    btnSerahkanObat.disabled = true;
                                    btnSerahkanObat.classList.replace('btn-success',
                                        'btn-secondary');
                                    btnSerahkanObat.innerHTML =
                                        `<i class="bi bi-check-circle me-1"></i> Sudah Diserahkan`;
                                } else {
                                    btnSerahkanObat.disabled = false;
                                    btnSerahkanObat.classList.replace('btn-secondary',
                                        'btn-success');
                                    btnSerahkanObat.innerHTML =
                                        `<i class="bi bi-box-seam me-1"></i> Serahkan Obat & Bridging SATUSEHAT`;
                                }
                            } else {
                                htmlRows =
                                    `<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada rincian obat lunas untuk kunjungan ini.</td></tr>`;
                            }

                            tbodyRincian.innerHTML = htmlRows;
                            document.getElementById('modal-total-obat').innerText = 'Rp ' +
                                grandTotal.toLocaleString('id-ID');
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            tbodyRincian.innerHTML =
                                `<tr><td colspan="5" class="text-center text-danger py-3">Gagal memuat rincian obat.</td></tr>`;
                        });
                });
            });

            // 1. Event Handler: Cetak Resep
            btnCetakResep.addEventListener('click', function() {
                if (!activeKunjunganId) return;
                window.open(`{{ url('/farmasi/cetak-resep') }}/${activeKunjunganId}`, '_blank');
            });

            // 2. Event Handler: Serahkan Obat & Bridging SATUSEHAT
            btnSerahkanObat.addEventListener('click', function() {
                if (!activeKunjunganId) return;

                if (!confirm(
                        'Apakah Anda yakin ingin menyerahkan obat dan mengirimkan data MedicationDispense ke SATUSEHAT?'
                    )) {
                    return;
                }

                // Tampilkan indikator loading pada tombol
                btnSerahkanObat.disabled = true;
                const originalText = btnSerahkanObat.innerHTML;
                btnSerahkanObat.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-1"></span> Mengirim ke SATUSEHAT...`;

                fetch(`{{ url('/farmasi/serahkan-obat') }}/${activeKunjunganId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Obat berhasil diserahkan dan data SATUSEHAT berhasil dikirim!');
                            btnSerahkanObat.classList.replace('btn-success', 'btn-secondary');
                            btnSerahkanObat.innerHTML =
                                `<i class="bi bi-check-circle me-1"></i> Sudah Diserahkan`;
                        } else {
                            alert('Gagal: ' + (data.message ||
                                'Terjadi kesalahan saat bridging SATUSEHAT.'));
                            btnSerahkanObat.disabled = false;
                            btnSerahkanObat.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan jaringan atau server.');
                        btnSerahkanObat.disabled = false;
                        btnSerahkanObat.innerHTML = originalText;
                    });
            });
        });
    </script>
@endsection
