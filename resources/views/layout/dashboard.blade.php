<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #0a0b10;
            --bg-card: #12141c;
            --border-color: #1f222d;
            --accent-color: #3d5afe;
            --text-dim: #8a8d98;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: var(--bg-main);
            color: #f1f1f1;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 260px;
            background: #0f111a;
            border-right: 1px solid var(--border-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        /* Mobile: sidebar hidden by default */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .sidebar.collapsed {
                width: 260px;
            }
            .main-content {
                margin-left: 0 !important;
            }
            .mobile-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                backdrop-filter: blur(2px);
            }
            .mobile-overlay.active {
                display: block;
            }
        }

        .mobile-header {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            background: #0f111a;
            border-bottom: 1px solid var(--border-color);
            z-index: 998;
            padding: 0 16px;
            align-items: center;
            justify-content: space-between;
        }

        @media (max-width: 991.98px) {
            .mobile-header {
                display: flex;
            }
            .main-content {
                padding-top: 76px !important;
            }
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-icon {
            width: 35px;
            height: 35px;
            background: var(--accent-color);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar.collapsed .logo-text {
            display: none;
        }

        .menu-item {
            padding: 14px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            color: var(--text-dim);
            text-decoration: none;
            transition: 0.2s;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .menu-item:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.03);
        }

        .menu-item.active {
            color: #fff;
            background: rgba(61, 90, 254, 0.1);
            border-right: 3px solid var(--accent-color);
        }

        .sidebar.collapsed .menu-text {
            display: none;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 40px;
            transition: 0.3s;
        }

        .sidebar.collapsed~.main-content {
            margin-left: 80px;
        }

        /* Card System - SEAMLESS LOOK */
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
        }

        /* TABLE DARK MODE FIX */
        .table {
            --bs-table-bg: transparent !important;
            --bs-table-color: #e0e0e0 !important;
            --bs-table-border-color: #1f222d !important;
            --bs-table-hover-bg: rgba(255, 255, 255, 0.02) !important;
        }

        .table thead th {
            color: var(--text-dim);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            border-bottom: 1px solid #1f222d !important;
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
        }

        .table tbody tr:hover td {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.05) !important;
            /* Warna hover yang lebih halus */
        }

        .table tbody tr:hover td * {
            color: #ffffff !important;
        }

        /* INPUT & FILTER DARK MODE FIX */
        .form-control,
        .form-select {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid var(--border-color) !important;
            color: #fff !important;
            border-radius: 8px;
        }

        .form-select option {
            background-color: #12141c !important;
            color: #fff !important;
            padding: 10px;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border-color: var(--accent-color) !important;
            color: #fff !important;
            box-shadow: 0 0 0 0.25rem rgba(61, 90, 254, 0.25) !important;
        }

        ::placeholder {
            color: rgba(255, 255, 255, 0.3) !important;
        }

        .text-label {
            color: var(--text-dim);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .btn-custom {
            border-radius: 6px;
            padding: 8px 18px;
            font-weight: 500;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="d-flex align-items-center gap-2">
            <div class="logo-icon" style="width:30px;height:30px;"><i class="fas fa-shield-alt text-white" style="font-size:12px;"></i></div>
            <span class="fw-bold text-white" style="font-size: 14px;">ADMIN CONTROL</span>
        </div>
        <button class="btn btn-sm text-white p-0" onclick="toggleMobileSidebar()" style="font-size:20px;">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleMobileSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="d-flex align-items-center gap-3">
                <div class="logo-icon"><i class="fas fa-shield-alt text-white"></i></div>
                <span class="logo-text fw-bold ls-1 text-white">ADMIN CONTROL</span>
            </div>
            <button class="btn btn-sm text-secondary p-0" onclick="toggleSidebar()"><i class="fas fa-outdent"></i></button>
        </div>

        <div class="mt-4">
            @role('admin')
            <a href="{{ route('dashboard') }}" class="menu-item {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> <span class="menu-text">Overview</span>
            </a>
            <a href="{{ route('admin.aspirations.index') }}" class="menu-item {{ Request::is('admin/input-aspirations*') ? 'active' : '' }}">
                <i class="fas fa-inbox"></i> <span class="menu-text">Aspirasi Siswa</span>
            </a>
            <a href="{{ route('admin.students.index') }}" class="menu-item {{ Request::is('admin/students*') ? 'active' : '' }}">
                <i class="fas fa-user-friends"></i> <span class="menu-text">Data Siswa</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="menu-item {{ Request::is('admin/categories*') ? 'active' : '' }}">
                <i class="fas fa-layer-group"></i> <span class="menu-text">Kategori</span>
            </a>
            @endrole

            @role('ketua_yayasan')
            <a href="{{ route('dashboard') }}" class="menu-item {{ Request::is('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span class="menu-text">Overview</span>
            </a>

            <a href="{{ route('ketua.aspirations.index') }}" class="menu-item {{ Request::is('ketua/aspirations*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i>
                <span class="menu-text">Persetujuan Aspirasi</span>
            </a>

            <a href="{{ route('ketua.reports.index') }}" class="menu-item {{ Request::is('ketua/reports*') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i>
                <span class="menu-text">Laporan Tahunan</span>
            </a>
            @endrole

            <div class="position-absolute bottom-0 w-100 p-3" style="border-top: 1px solid var(--border-color);">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-link text-danger text-decoration-none d-flex align-items-center gap-2 p-0 w-100">
                        <i class="fas fa-power-off"></i> <span class="menu-text">Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            const icon = sidebar.querySelector('.fa-outdent, .fa-indent');
            if (icon) {
                icon.classList.toggle('fa-outdent');
                icon.classList.toggle('fa-indent');
            }
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        }
    </script>

    @stack('scripts')
</body>

</html>