@extends('Template.Main')
@section('container')
    <div class="container-fluid pt-3">
        <!-- HEADER & TITLE -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Pembayaran
                </h4>
                <small class="text-muted">Daftar transaksi pembayaran transaksi kasir & farmasi yang telah lunas.</small>
            </div>
        </div>

        <!-- FILTER & SUMMARY CARD -->
        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <form method="GET" action="{{ route('indexriwayatpembayaran') }}"
                            class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label text-muted small mb-1">Pilih Tanggal Awal</label>
                                <input type="date" name="tanggalawal" id="tanggalawal"
                                    class="form-control form-control-sm" value="{{ $tanggal }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small mb-1">Pilih Tanggal Akhir</label>
                                <input type="date" name="tanggalakhir" id="tanggalakhir"
                                    class="form-control form-control-sm" value="{{ $tanggal }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a class="btn btn-sm btn-light border w-100 mt-4" onclick="caridatariwayat()">
                                    Cari
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL DATA RIWAYAT -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <div class="v_tabel_nya">

                    </div>
                </div>
            </div>
      
        </div>
    </div>
    <div class="modal fade" id="modalDetailBilling" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i>Detail Billing Pembayaran
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalDetailContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="text-muted small mt-2">Memuat data billing...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            caridatariwayat();
        });

        function caridatariwayat() {
            tanggalawal = $('#tanggalawal').val()
            tanggalakhir = $('#tanggalakhir').val()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggalawal,
                    tanggalakhir
                },
                url: '<?= route('ambil_riwayat_pembayaran') ?>',
                success: function(response) {
                    $('.v_tabel_nya').html(response);
                }
            });
        }
    </script>
@endsection
