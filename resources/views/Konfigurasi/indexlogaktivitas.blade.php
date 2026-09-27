@extends('Template.Main')

@section('container')
    <!-- Content Header -->
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Log Login User</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Audit System</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Log Login</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="app-content">
        <div class="container-fluid">

            <!-- STATISTIC CARDS -->
            <div class="row mb-3">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-primary text-white"><i class="fas fa-sign-in-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Login</span>
                            <span class="info-box-number fs-5">{{ number_format($logs->total() ?? count($logs)) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-success text-white"><i class="fas fa-mobile-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Login Mobile</span>
                            <span class="info-box-number fs-5">
                                {{ $logs->where('device_type', 'Mobile')->count() }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-info text-white"><i class="fas fa-desktop"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Login Desktop</span>
                            <span class="info-box-number fs-5">
                                {{ $logs->where('device_type', 'Desktop')->count() }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-warning text-white"><i class="fas fa-globe"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">IP Terdeteksi</span>
                            <span class="info-box-number fs-5">
                                {{ $logs->pluck('ip_address')->unique()->count() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN TABLE CARD -->
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header d-flex justify-content-between align-middle">
                    <h3 class="card-title fw-bold my-auto">
                        <i class="fas fa-history me-1"></i> Riwayat Aktivitas Login
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle" id="table-login-logs"
                            style="font-size: 0.85rem;">
                            <thead class="table-dark text-center align-middle">
                                <tr>
                                    <th style="width: 4%;">No</th>
                                    <th style="width: 15%;">Waktu Login</th>
                                    <th style="width: 18%;">User / Nama</th>
                                    <th style="width: 12%;">IP Address</th>
                                    <th style="width: 18%;">Lokasi (Kota/Wilayah)</th>
                                    <th style="width: 15%;">Perangkat & Platform</th>
                                    <th style="width: 10%;">Browser</th>
                                    <th style="width: 8%;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $index => $row)
                                    <tr>
                                        <td class="text-center">
                                            {{ method_exists($logs, 'firstItem') ? $logs->firstItem() + $index : $index + 1 }}
                                        </td>

                                        <!-- Waktu Login -->
                                        <td class="text-center">
                                            <span class="fw-bold d-block">
                                                {{ \Carbon\Carbon::parse($row->login_at)->format('d/m/Y H:i:s') }}
                                            </span>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($row->login_at)->diffForHumans() }}
                                            </small>
                                        </td>

                                        <!-- User Info -->
                                        <td>
                                            <div class="fw-bold text-primary">
                                                {{ $row->user->nama ?? ($row->user->username ?? 'Guest / Unknown') }}
                                            </div>
                                            <small class="text-muted">
                                                @ {{ $row->user->username ?? '-' }}
                                            </small>
                                        </td>

                                        <!-- IP Address -->
                                        <td class="font-monospace text-center">
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-network-wired me-1"></i>{{ $row->ip_address }}
                                            </span>
                                        </td>

                                        <!-- Lokasi -->
                                        <td>
                                            @if ($row->city || $row->region || $row->country)
                                                <div class="fw-semibold">
                                                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                    {{ $row->city ?? '-' }}, {{ $row->region ?? '-' }}
                                                </div>
                                                <small class="text-muted">{{ $row->country ?? 'Indonesia' }}</small>
                                            @else
                                                <span class="text-muted italic"><i class="fas fa-map-slash me-1"></i> Tidak
                                                    terdeteksi</span>
                                            @endif
                                        </td>

                                        <!-- Perangkat & OS -->
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($row->device_type == 'Mobile')
                                                    <i class="fas fa-mobile-alt text-success fs-5"></i>
                                                @elseif($row->device_type == 'Tablet')
                                                    <i class="fas fa-tablet-alt text-info fs-5"></i>
                                                @else
                                                    <i class="fas fa-desktop text-secondary fs-5"></i>
                                                @endif
                                                <div>
                                                    <span
                                                        class="fw-bold d-block">{{ $row->device_type ?? 'Desktop' }}</span>
                                                    <small class="text-muted">{{ $row->platform ?? 'Unknown OS' }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Browser -->
                                        <td>
                                            <span class="badge bg-outline-secondary text-dark border">
                                                <i class="fas fa-globe me-1"></i>{{ $row->browser ?? 'Unknown' }}
                                            </span>
                                        </td>

                                        <!-- Status -->
                                        <td class="text-center">
                                            @if ($row->is_successful)
                                                <span class="badge bg-success px-2 py-1">
                                                    <i class="fas fa-check-circle me-1"></i> Sukses
                                                </span>
                                            @else
                                                <span class="badge bg-danger px-2 py-1">
                                                    <i class="fas fa-times-circle me-1"></i> Gagal
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle fs-4 d-block mb-2"></i>
                                            Belum ada riwayat log login user yang tercatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if (method_exists($logs, 'links'))
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                Menampilkan {{ $logs->firstItem() ?? 0 }} sampai {{ $logs->lastItem() ?? 0 }} dari
                                {{ $logs->total() ?? 0 }} data
                            </small>
                            <div>
                                {{ $logs->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
