@extends('Template.Main')
@section('container')
    <div class="v_1">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0">Data Pasien</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Data Pasien</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">

                <!-- Card Filter Tanggal Masuk Pasien -->
                <div class="card card-outline card-primary mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-calendar-range-fill me-1"></i> Filter Tanggal Masuk Pasien
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="formFilterTanggal">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label for="tgl_awal" class="form-label fw-bold">Tanggal Awal</label>
                                    <input type="date" class="form-control" id="tgl_awal" name="tgl_awal"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="tgl_akhir" class="form-label fw-bold">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-4 d-flex gap-2">
                                    <button type="button" class="btn btn-primary w-100" onclick="loadTabelPasien()">
                                        <i class="bi bi-search me-1"></i> Cari Data
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="resetFilter()">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Card Tabel Data Pasien -->
                <div class="card card-outline card-info mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="bi bi-table me-1"></i> Tabel Data Pasien
                        </h3>
                    </div>
                    <div class="card-body">
                        <!-- Loading State -->
                        <div class="text-center py-4 d-none" id="loadingState">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted mb-0">Memuat data pasien...</p>
                        </div>

                        <!-- Container Tempat Tabel Dimuat -->
                        <div class="v_tabel_pasien mt-2"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div hidden class="v_2">
        <div class="app-content">
            <div class="container mt-2">
                <div class="v_formnya"></div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT AJAX FILTER -->
    <script>
        $(document).ready(function() {
            // Load data otomatis saat halaman pertama kali dibuka
            loadTabelPasien();
        });

        function loadTabelPasien() {
            let tglAwal = $('#tgl_awal').val();
            let tglAkhir = $('#tgl_akhir').val();

            if (!tglAwal || !tglAkhir) {
                alert('Silakan pilih tanggal awal dan tanggal akhir terlebih dahulu.');
                return;
            }

            $('#loadingState').removeClass('d-none');
            $('.v_tabel_pasien').html('');

            $.ajax({
                url: "{{ route('pasien.getTabelPasien') }}", // Sesuaikan dengan route controller kamu
                type: "GET",
                data: {
                    tgl_awal: tglAwal,
                    tgl_akhir: tglAkhir
                },
                success: function(response) {
                    $('#loadingState').addClass('d-none');
                    $('.v_tabel_pasien').html(response);
                },
                error: function(xhr) {
                    $('#loadingState').addClass('d-none');
                    $('.v_tabel_pasien').html(`
                        <div class="alert alert-danger text-center">
                            Gagal memuat data. Silakan coba lagi.
                        </div>
                    `);
                }
            });
        }

        function resetFilter() {
            $('#tgl_awal').val("{{ date('Y-m-01') }}");
            $('#tgl_akhir').val("{{ date('Y-m-d') }}");
            loadTabelPasien();
        }

        function kembali() {
            $('.v_1').removeAttr('hidden');
            $('.v_2').attr('hidden', true);
        }
    </script>
@endsection
