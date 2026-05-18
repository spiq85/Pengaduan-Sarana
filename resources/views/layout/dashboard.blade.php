<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');

        :root {
            --bg-main: #f6f8ff;
            --bg-main-2: #eef3ff;
            --bg-card: #ffffff;
            --border-color: #e5eaff;
            --accent-color: #7a5af8;
            --accent-soft: #f0ebff;
            --info-color: #5b8dff;
            --text-dim: #6b6f94;
            --text-main: #1f2759;
            --shadow-soft: 0 14px 38px rgba(85, 100, 210, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Manrope', 'Segoe UI', sans-serif;
            background:
                radial-gradient(circle at 8% 12%, #f3eaff 0%, rgba(243, 234, 255, 0) 40%),
                radial-gradient(circle at 92% 16%, #e3edff 0%, rgba(227, 237, 255, 0) 34%),
                linear-gradient(160deg, var(--bg-main) 0%, var(--bg-main-2) 100%);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 260px;
            background: linear-gradient(190deg, #ffffff 0%, #f6f9ff 100%);
            border-right: 1px solid var(--border-color);
            box-shadow: 8px 0 30px rgba(85, 100, 210, 0.08);
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
                background: rgba(0, 0, 0, 0.5);
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
            background: rgba(255, 255, 255, 0.9);
            border-bottom: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
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
            background: linear-gradient(140deg, #7a5af8 0%, #5b8dff 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 22px rgba(122, 90, 248, 0.28);
        }

        .sidebar.collapsed .logo-text {
            display: none;
        }

        .menu-item {
            padding: 14px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            color: #656c99;
            text-decoration: none;
            transition: 0.2s;
            font-size: 0.95rem;
            font-weight: 700;
            border-radius: 12px;
            margin: 2px 8px;
        }

        .menu-item:hover {
            color: #273372;
            background: linear-gradient(90deg, #f0ebff 0%, #eaf1ff 100%);
            transform: translateX(3px);
        }

        .menu-item.active {
            color: #1f2759;
            background: linear-gradient(95deg, #ece4ff 0%, #e7f0ff 100%);
            border-right: 3px solid #7a5af8;
            box-shadow: 0 10px 24px rgba(122, 90, 248, 0.2);
        }

        .sidebar.collapsed .menu-text {
            display: none;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 40px;
            transition: 0.3s;
            animation: fadeInUp 0.45s ease-out;
        }

        .sidebar.collapsed~.main-content {
            margin-left: 80px;
        }

        /* Card System - SEAMLESS LOOK */
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            padding: 24px;
            box-shadow: var(--shadow-soft);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 34px rgba(85, 100, 210, 0.16);
        }

        /* TABLE DARK MODE FIX */
        .table {
            --bs-table-bg: transparent !important;
            --bs-table-color: #293367 !important;
            --bs-table-border-color: #e7ecff !important;
            --bs-table-hover-bg: #f3f7ff !important;
        }

        .table thead th {
            color: var(--text-dim);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            border-bottom: 1px solid #e7ecff !important;
            background: #f8faff;
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
        }

        .table tbody tr:hover td {
            color: #1f2759 !important;
            background-color: #f3f7ff !important;
        }

        .table tbody tr:hover td * {
            color: #2f2359 !important;
        }

        /* INPUT & FILTER DARK MODE FIX */
        .form-control,
        .form-select {
            background-color: #ffffff !important;
            border: 1px solid #dbe4ff !important;
            color: #243062 !important;
            border-radius: 8px;
        }

        .form-select option {
            background-color: #ffffff !important;
            color: #243062 !important;
            padding: 10px;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #ffffff !important;
            border-color: #7a5af8 !important;
            color: #243062 !important;
            box-shadow: 0 0 0 0.25rem rgba(122, 90, 248, 0.14) !important;
        }

        ::placeholder {
            color: #9ea8cd !important;
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

        .btn-primary {
            background: linear-gradient(135deg, #7a5af8 0%, #5b8dff 100%);
            border-color: #7a5af8;
            box-shadow: 0 10px 20px rgba(122, 90, 248, 0.22);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #6848eb 0%, #4d7df2 100%);
            border-color: #6848eb;
        }

        .btn-outline-secondary {
            color: #5c699b !important;
            border-color: #cad7ff !important;
            background: #fff !important;
        }

        .btn-outline-secondary:hover {
            color: #1f2759 !important;
            background: #edf4ff !important;
        }

        .main-content .text-white {
            color: #1f2759 !important;
        }

        .main-content .text-white-50,
        .main-content .text-secondary {
            color: #6b739f !important;
        }

        .main-content .bg-dark {
            background: #f5f8ff !important;
            color: #1f2759 !important;
            border-color: #dee6ff !important;
        }

        .main-content .border-secondary {
            border-color: #dbe4ff !important;
        }

        .main-content [style*="#1a1d21"],
        .main-content [style*="#161822"],
        .main-content [style*="#12141c"],
        .main-content [style*="#0f111a"] {
            background: #ffffff !important;
            border-color: #e5eaff !important;
            color: #1f2759 !important;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="d-flex align-items-center gap-2">
            <div class="logo-icon" style="width:30px;height:30px;"><i class="fas fa-shield-alt text-white" style="font-size:12px;"></i></div>
            <span class="fw-bold" style="font-size: 14px; color:#2f2359;">ADMIN CONTROL</span>
        </div>
        <button class="btn btn-sm p-0" onclick="toggleMobileSidebar()" style="font-size:20px;color:#5f4a9c;">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleMobileSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="d-flex align-items-center gap-3">
                <div class="logo-icon"><i class="fas fa-shield-alt text-white"></i></div>
                <span class="logo-text fw-bold ls-1" style="color:#2f2359;">ADMIN CONTROL</span>
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
            <a href="{{ route('admin.locations.index') }}" class="menu-item {{ Request::is('admin/locations*') ? 'active' : '' }}">
                <i class="fas fa-map-marked-alt"></i> <span class="menu-text">Lokasi</span>
            </a>
            <a href="{{ route('admin.password-resets.index') }}" class="menu-item {{ Request::is('admin/password-resets*') ? 'active' : '' }}">
                <i class="fas fa-key"></i> <span class="menu-text">Reset Password</span>
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
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <ul class="mb-0 list-unstyled">
                    @foreach($errors->all() as $error)
                        <li><i class="fas fa-times-circle me-2"></i> {{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

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