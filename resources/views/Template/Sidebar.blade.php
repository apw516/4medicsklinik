<style>
    /* Custom Theme Sidebar Merah Pias */
    aside.app-sidebar.custom-red-sidebar {
        background-color: #8b262a !important;
        /* Merah pias gelap agar teks putih kontras */
        color: #f8f9fa !important;
    }

    /* Penonjolan Area Logo Brand */
    aside.app-sidebar.custom-red-sidebar .sidebar-brand {
        background-color: #6e1c20 !important;
        /* Latar lebih gelap untuk area brand */
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        padding: 12px 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    aside.app-sidebar.custom-red-sidebar .brand-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 5px;
        background: rgba(255, 255, 255, 0.08);
        /* Card pemanis di belakang logo */
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    aside.app-sidebar.custom-red-sidebar .brand-link:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    aside.app-sidebar.custom-red-sidebar .brand-image {
        max-height: 45px;
        width: auto;
        object-fit: contain;
        filter: drop-shadow(0px 2px 4px rgba(0, 0, 0, 0.4));
        /* Efek shadow agar logo menonjol */
    }

    /* Styling Menu & Teks Sidebar */
    aside.app-sidebar.custom-red-sidebar .nav-header {
        color: #f1aeb5 !important;
        /* Warna merah muda soft/pias untuk header section */
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 0.8px;
        margin-top: 10px;
    }

    aside.app-sidebar.custom-red-sidebar .nav-link {
        color: #e9ecef !important;
        /* Teks putih gading agar kontras & jelas */
        font-weight: 500;
    }

    aside.app-sidebar.custom-red-sidebar .nav-link i {
        color: #f8d7da !important;
        /* Icon warna soft red terang */
    }

    /* State Active & Hover pada Menu */
    aside.app-sidebar.custom-red-sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.12) !important;
        color: #ffffff !important;
    }

    aside.app-sidebar.custom-red-sidebar .nav-link.active {
        background-color: #d9534f !important;
        /* Merah pias terang untuk menu aktif */
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    aside.app-sidebar.custom-red-sidebar .nav-link.active i {
        color: #ffffff !important;
    }

    /* Submenu Treeview Styling */
    aside.app-sidebar.custom-red-sidebar .nav-treeview .nav-link {
        padding-left: 2.5rem;
        background-color: rgba(0, 0, 0, 0.15);
    }
</style>

<aside class="app-sidebar custom-red-sidebar shadow" data-bs-theme="dark">
    <!-- Area Logo Brand yang Ditonjolkan -->
    <div class="sidebar-brand">
        <a href="./index.html" class="brand-link">
            <img src="./public/img/logosidebar.png" alt="4medics Logo" class="brand-image" />
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                aria-label="Main navigation" data-accordion="false" id="navigation">

                <li class="nav-item @if ($menu == 'dashboard') menu-open @endif">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>
                            Dashboard
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="" class="nav-link @if ($menu == 'dashboard') active @endif">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Dashboard 4medics</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">REKAMEDIS</li>
                <li class="nav-item">
                    <a href="{{ route('indexmasterpasien') }}"
                        class="nav-link @if ($menu == 'masterpasien') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Master Pasien</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexriwayatkunjungan') }}"
                        class="nav-link @if ($menu == 'riwayatkunjungan') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Riwayat Kunjungan</p>
                    </a>
                </li>

                @if (auth()->user()->hak_akses != 2)
                    <li class="nav-header">DOKTER</li>
                    <li class="nav-item">
                        <a href="{{ route('indexdokter') }}"
                            class="nav-link @if ($menu == 'indexdatapasienklinik') active @endif">
                            <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                            <p>Data Pasien</p>
                        </a>
                    </li>
                @endif

                <li class="nav-header">KASIR & FARMASI</li>
                @if (auth()->user()->hak_akses != 2)
                    <li class="nav-item">
                        <a href="{{ route('indexkasir') }}"
                            class="nav-link @if ($menu == 'indexdatapasienkasir') active @endif">
                            <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                            <p>Antrian Kasir</p>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a href="{{ route('indexfarmasi') }}"
                        class="nav-link @if ($menu == 'indexdatapasienfarmasi') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Antrian Farmasi</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexriwayatpembayaran') }}"
                        class="nav-link @if ($menu == 'indexriwayatpembayaran') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Riwayat Pembayaran</p>
                    </a>
                </li>

                @if (auth()->user()->hak_akses != 2)
                    <li class="nav-header">DATA MASTER</li>
                    <li class="nav-item">
                        <a href="{{ route('indexmastertarif') }}"
                            class="nav-link @if ($menu == 'indexmastertarif') active @endif">
                            <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                            <p>Master Tarif</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('indexmasterobat') }}"
                            class="nav-link @if ($menu == 'indexmasterobat') active @endif">
                            <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                            <p>Master Obat</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('indexorganization') }}"
                            class="nav-link @if ($menu == 'indexorganization') active @endif">
                            <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                            <p>Master Organization</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('indexmasterunit') }}"
                            class="nav-link @if ($menu == 'indexmasterunit') active @endif">
                            <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                            <p>Master Unit</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('indexmasterpractitioner') }}"
                            class="nav-link @if ($menu == 'indexmasterpractitioner') active @endif">
                            <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                            <p>Master Practitioner</p>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->nama == 'agyl')
                <li class="nav-header">RIWAYAT AKSES</li>
                <li class="nav-item">
                    <a href="{{ route('indexmasteruser') }}"
                        class="nav-link @if ($menu == 'indexmasteruser') active @endif">
                        <i class="nav-icon bi bi-file-bar-graph-fill"></i>
                        <p>Master User</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('indexriwayatakses') }}"
                        class="nav-link @if ($menu == 'indexriwayatakses') active @endif">
                        <i class="nav-icon bi bi-person-vcard"></i>
                        <p class="text">Log aktivitas</p>
                    </a>
                </li>
                @endif
                <li class="nav-header">INFO AKUN</li>
                <li class="nav-item">
                    <a href="{{ route('indexdetailakun') }}"
                        class="nav-link @if ($menu == 'indexdetailakun') active @endif">
                        <i class="nav-icon bi bi-person-vcard"></i>
                        <p class="text">Detail Akun</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="logout()">
                        <i class="nav-icon bi bi-box-arrow-left"></i>
                        <p>Logout</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
