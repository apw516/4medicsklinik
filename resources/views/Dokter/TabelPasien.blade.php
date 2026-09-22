<div class="card shadow-sm border-0 rounded-3 overflow-hidden p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 w-100" id="dtablePasien">
            <thead class="table-dark text-nowrap">
                <tr>
                    <th width="4%" class="text-center py-3">No</th>
                    <th width="10%" class="py-3">No. RM</th>
                    <th width="22%" class="py-3">Identitas Pasien</th>
                    <th width="14%" class="py-3">NIK</th>
                    <th width="8%" class="text-center py-3">Gender</th>
                    <th width="16%" class="py-3">Pengisi ERM</th>
                    <th width="12%" class="text-center py-3">Status</th>
                    <th width="14%" class="text-center py-3">Aksi</th>
                </tr>
            </thead>
            <tbody id="bodyTabelPasien">
                @forelse ($kunjungans as $index => $kunjungan)
                    @php
                        $pasien = $kunjungan->pasien;
                        $ermData = $kunjungan->erm;
                    @endphp
                    <tr>
                        <!-- NO -->
                        <td class="text-center fw-bold text-secondary">{{ $index + 1 }}</td>

                        <!-- NO RM -->
                        <td>
                            <span class="badge bg-light text-dark border font-monospace px-2 py-1 fs-6">
                                <i class="bi bi-card-heading text-primary me-1"></i>{{ $pasien->no_rm ?? '-' }}
                            </span>
                        </td>

                        <!-- PASIEN & IHS -->
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $pasien->nama_lengkap ?? '-' }}</div>
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-hospital me-1"></i>IHS: <code>{{ $pasien->ihs_number ?? '-' }}</code>
                            </small>
                        </td>

                        <!-- NIK -->
                        <td>
                            <span class="text-secondary font-monospace">{{ $pasien->nik ?? '-' }}</span>
                        </td>

                        <!-- GENDER -->
                        <td class="text-center">
                            @if (($pasien->jenis_kelamin ?? '') == 'L')
                                <span class="badge bg-info-subtle text-info-emphasis border border-info px-2 py-1">
                                    <i class="bi bi-gender-male me-1"></i> L
                                </span>
                            @elseif (($pasien->jenis_kelamin ?? '') == 'P')
                                <span
                                    class="badge bg-danger-subtle text-danger-emphasis border border-danger px-2 py-1">
                                    <i class="bi bi-gender-female me-1"></i> P
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary">-</span>
                            @endif
                        </td>

                        <!-- STATUS ERM & PENGISI -->
                        <td>
                            @if ($ermData)
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-check-fill text-success fs-5 me-2"></i>
                                    <div>
                                        <div class="fw-semibold text-dark small" style="line-height: 1.2;">
                                            {{ $ermData->dokter->nama_dokter ?? ($ermData->dokter->nama ?? ($kunjungan->dokter->nama_dokter ?? 'Dokter')) }}
                                        </div>
                                        <small class="text-success" style="font-size: 0.75rem;">
                                            <i class="bi bi-check2 me-1"></i>ERM Terisi
                                        </small>
                                    </div>
                                </div>
                            @else
                                <span
                                    class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Belum Diisi
                                </span>
                            @endif
                        </td>

                        <!-- STATUS KUNJUNGAN -->
                        <td class="text-center">
                            @if ($kunjungan->status_kunjungan == 'SELESAI')
                                <span class="badge bg-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                </span>
                            @else
                                <span class="badge bg-warning text-dark border border-warning-subtle px-2 py-1">
                                    <i class="bi bi-clock-history me-1"></i>
                                    {{ $kunjungan->status_kunjungan ?? 'PROSES' }}
                                </span>
                            @endif
                        </td>

                        <!-- AKSI -->
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <!-- Tombol Utama: Input / Edit ERM -->
                                <button type="button"
                                    class="btn btn-sm {{ $ermData ? 'btn-outline-primary' : 'btn-primary' }}"
                                    onclick="openErmForm('{{ $kunjungan->id }}')" data-bs-toggle="tooltip"
                                    title="{{ $ermData ? 'Edit ERM Klinik' : 'Input ERM Klinik' }}">
                                    <i class="bi {{ $ermData ? 'bi-pencil-square' : 'bi-journal-medical' }} me-1"></i>
                                    {{ $ermData ? 'Edit' : 'Input' }}
                                </button>

                                <!-- Dropdown Aksi Tambahan (Detail & Riwayat) -->
                                <button type="button"
                                    class="btn btn-sm {{ $ermData ? 'btn-outline-primary' : 'btn-primary' }} dropdown-toggle dropdown-toggle-split"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li>
                                        <a class="dropdown-item small" href="javascript:void(0)"
                                            onclick="openDetailKunjungan('{{ $kunjungan->id }}')">
                                            <i class="bi bi-person-lines-fill text-info me-2"></i> Detail Kunjungan
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item small" href="javascript:void(0)"
                                            onclick="openRiwayatKunjungan('{{ $pasien->id ?? $kunjungan->pasien_id }}')">
                                            <i class="bi bi-clock-history text-warning me-2"></i> Riwayat Rekam Medis
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item small" href="javascript:void(0)"
                                            onclick="batalDataPeriksa('{{ $kunjungan->id }}')">
                                            <i class="bi bi-trash3 text-danger me-2"></i>
                                            Batal data periksa
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <!-- Kosongkan <tr> di sini jika menggunakan DataTables Client-Side -->
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Container untuk Detail & Riwayat -->
<div class="modal fade" id="modalLayananPasien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalTitle">Layanan Pasien</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBodyContent">
                <!-- Konten AJAX dimuat di sini -->
            </div>
        </div>
    </div>
</div>

<script>
    let tablePasien;

    $(document.body).ready(function() {
        // 1. Inisialisasi DataTables
        if (!$.fn.DataTable.isDataTable('#dtablePasien')) {
            tablePasien = $('#dtablePasien').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "Semua"]
                ],
                order: [
                    [0, 'asc']
                ], // Order berdasarkan No
                columnDefs: [{
                        targets: [0, 4, 6, 7],
                        orderable: false
                    }, // Nonaktifkan sorting pada kolom aksi/gender/status/no
                    {
                        targets: [7],
                        className: 'text-center'
                    }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari Pasien, RM, NIK...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pasien",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 pasien",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Data pasien tidak ditemukan",
                    paginate: {
                        first: "<i class='bi bi-chevron-double-left'></i>",
                        last: "<i class='bi bi-chevron-double-right'></i>",
                        next: "<i class='bi bi-chevron-right'></i>",
                        previous: "<i class='bi bi-chevron-left'></i>"
                    }
                },
                drawCallback: function() {
                    // Re-inisialisasi Bootstrap Tooltip setelah ganti halaman / filter
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll(
                        '[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }
            });
        }
    });

    // 2. Fungsi Membuka Form ERM Klinik (Mengganti View V_1 ke V_2)
    function openErmForm(pasienId) {
        showLoadingV2();
        $('.v_1').attr('hidden', true);
        $('.v_2').removeAttr('hidden');

        $.ajax({
            url: "{{ url('/erm/form-input') }}/" + pasienId,
            type: "GET",
            success: function(response) {
                $('.v_formnya').html(response);
            },
            error: function(xhr) {
                $('.v_formnya').html(`
                    <div class="alert alert-danger shadow-sm">
                        <i class="bi bi-exclamation-octagon-fill me-2"></i>
                        Gagal memuat Form ERM Klinik: ${xhr.statusText}
                    </div>
                    <button class="btn btn-secondary mt-2" onclick="kembali()"><i class="bi bi-arrow-left me-1"></i> Kembali</button>
                `);
            }
        });
    }

    // 3. Fungsi Membuka Detail Kunjungan Pasien
    function openDetailKunjungan(pasienId) {
        $('#modalTitle').html('<i class="bi bi-person-lines-fill me-2"></i> Detail Kunjungan Pasien');
        $('#modalBodyContent').html(
            '<div class="text-center py-5"><div class="spinner-border text-primary me-2"></div><span class="align-middle">Memuat detail...</span></div>'
        );
        $('#modalLayananPasien').modal('show');

        $.ajax({
            url: "{{ url('/kunjungan/detail') }}/" + pasienId,
            type: "GET",
            success: function(response) {
                $('#modalBodyContent').html(response);
            },
            error: function(xhr) {
                $('#modalBodyContent').html(
                    '<div class="alert alert-danger m-3"><i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal memuat detail kunjungan.</div>'
                );
            }
        });
    }

    // 4. Fungsi Membuka Riwayat Rekam Medis / Kunjungan Pasien
    function openRiwayatKunjungan(pasienId) {
        $('#modalTitle').html('<i class="bi bi-clock-history me-2"></i> Riwayat Kunjungan & Rekam Medis');
        $('#modalBodyContent').html(
            '<div class="text-center py-5"><div class="spinner-border text-warning me-2"></div><span class="align-middle">Memuat riwayat...</span></div>'
        );
        $('#modalLayananPasien').modal('show');

        $.ajax({
            url: "{{ url('/kunjungan/riwayat') }}/" + pasienId,
            type: "GET",
            success: function(response) {
                $('#modalBodyContent').html(response);
            },
            error: function(xhr) {
                $('#modalBodyContent').html(
                    '<div class="alert alert-danger m-3"><i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal memuat riwayat kunjungan.</div>'
                );
            }
        });
    }
    // 4. Fungsi Membuka Riwayat Rekam Medis / Kunjungan Pasien
    function batalDataPeriksa(idkunjungan) {
        Swal.fire({
            title: "Anda yakin ?",
            text: "Data erm yang sudah diisi hari ini akan dihapus ...",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus !"
        }).then((result) => {
            $.ajax({
                url: "{{ url('/pasien/batalisierm') }}/" + idkunjungan,
                type: "GET", // Disarankan ganti ke POST/PUT jika mengubah data
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message
                    }).then(() => {
                        location.reload(); // Reload halaman jika diperlukan
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan sistem.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: errorMessage
                    });
                }
            });
        });

    }

    function showLoadingV2() {
        $('.v_formnya').html(`
            <div class="card p-5 text-center border-0 shadow-sm">
                <div class="spinner-border text-primary mx-auto mb-3" style="width: 3rem; height: 3rem;"></div>
                <h5 class="fw-bold">Memuat Form ERM Klinik...</h5>
                <p class="text-muted small">Mohon tunggu sebentar, sedang menyinkronkan data pasien.</p>
            </div>
        `);
    }
</script>
