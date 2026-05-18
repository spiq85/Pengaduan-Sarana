@extends('layout.dashboard')
@section('title', 'Aspirasi Siswa')

@section('content')
<style>
    .page-headline {
        font-weight: 900;
        letter-spacing: 0.2px;
        background: linear-gradient(120deg, #2563eb 0%, #0f172a 70%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-subtitle {
        color: #64748b;
        font-weight: 500;
    }

    .page-actions .btn {
        border-radius: 12px;
        font-weight: 700;
        padding: 0.55rem 0.95rem;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
    }

    .filter-panel {
        border: none;
        border-radius: 18px;
        background: linear-gradient(155deg, #ffffff 0%, #eef4ff 45%, #f8fbff 100%);
        padding: 1rem;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
    }

    .filter-panel .form-control,
    .filter-panel .form-select {
        border-radius: 10px;
        border: none;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.2);
    }

    .filter-panel .form-control:focus,
    .filter-panel .form-select:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 0.18rem rgba(59, 130, 246, 0.12);
    }

    .filter-title {
        font-size: 0.74rem;
        font-weight: 900;
        letter-spacing: 0.9px;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0.65rem;
    }

    .vote-badge {
        background: #eef4ff;
        border: none;
        color: #2563eb;
        font-weight: 800;
        padding: 0.4rem 0.7rem;
        border-radius: 12px;
        box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.15);
    }

    .custom-rank {
        min-width: 96px;
        display: inline-flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.15rem;
        padding: 0.4rem 0.65rem;
        border-radius: 0.8rem;
        font-size: 0.7rem;
        font-weight: 900;
        letter-spacing: 0.5px;
        line-height: 1.05;
    }

    .custom-rank.is-top {
        background: linear-gradient(135deg, #fff1f2, #ffe4e6);
        color: #be123c;
        border: 1px solid #fda4af;
    }

    .custom-rank.is-second {
        background: linear-gradient(135deg, #fff7ed, #ffedd5);
        color: #c2410c;
        border: 1px solid #fdba74;
    }

    .custom-rank.is-other {
        background: rgba(239, 68, 68, 0.08);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.18);
    }

    .asp-card {
        position: relative;
        border: none;
        border-radius: 20px;
        overflow: hidden;
        background: linear-gradient(160deg, #ffffff 0%, #f3f7ff 46%, #f8fbff 100%);
        box-shadow: 0 16px 34px rgba(15, 23, 42, 0.1);
    }

    .asp-card.tone-success {
        background: linear-gradient(160deg, #ffffff 0%, #effdf4 50%, #f7fff9 100%);
    }

    .asp-card.tone-danger {
        background: linear-gradient(160deg, #ffffff 0%, #fff1f2 50%, #fff7f8 100%);
    }

    .asp-card.tone-warning {
        background: linear-gradient(160deg, #ffffff 0%, #fff7ed 50%, #fffbf5 100%);
    }

    .asp-card.tone-info {
        background: linear-gradient(160deg, #ffffff 0%, #eef8ff 50%, #f8fcff 100%);
    }

    .asp-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 18px;
        border-bottom: none;
        background: transparent;
    }

    .asp-head-main {
        display: flex;
        align-items: center;
        gap: 0.7rem;
    }

    .asp-icon {
        width: 34px;
        height: 34px;
        border-radius: 11px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        color: #fff;
    }

    .asp-icon.success {
        background: linear-gradient(135deg, #22c55e, #15803d);
    }

    .asp-icon.danger {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
    }

    .asp-icon.warning {
        background: linear-gradient(135deg, #f59e0b, #b45309);
    }

    .asp-icon.info {
        background: linear-gradient(135deg, #0ea5e9, #0369a1);
    }

    .asp-card-title {
        font-size: 0.85rem;
        font-weight: 900;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin: 0;
    }

    .asp-card-count {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.72rem;
        font-weight: 800;
        color: #334155;
        border: none;
        background: rgba(255, 255, 255, 0.85);
        border-radius: 999px;
        padding: 0.25rem 0.55rem;
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.25);
    }

    .asp-toggle-btn {
        border: none;
        background: rgba(255, 255, 255, 0.92);
        color: #334155;
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.7px;
        text-transform: uppercase;
        border-radius: 999px;
        padding: 6px 10px;
        box-shadow: inset 0 0 0 1px rgba(148, 163, 184, 0.25);
    }

    .asp-toggle-btn:hover {
        background: #f1f5f9;
    }

    .asp-card-body {
        overflow-y: hidden;
        max-height: 1200px;
        opacity: 1;
        transform: translateY(0);
        transition: max-height 0.35s ease, opacity 0.25s ease, transform 0.28s ease;
    }

    .asp-card-body.table-responsive {
        overflow-x: auto;
    }

    .asp-card.is-collapsed .asp-card-body {
        max-height: 0;
        opacity: 0;
        transform: translateY(-10px);
    }

    .asp-card.is-collapsed .asp-card-header {
        padding-bottom: 13px;
    }

    .lock-note {
        margin-top: 0.5rem;
        font-size: 0.74rem;
        color: #b45309;
        font-weight: 700;
    }

    .table-modern thead tr th {
        background: transparent;
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 900;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        border: none;
        padding-bottom: 0.55rem;
    }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0 8px;
        margin-top: -4px;
        margin-bottom: 8px !important;
        min-width: 860px;
    }

    .table-modern tbody tr td {
        border: none;
        padding-top: 0.78rem;
        padding-bottom: 0.78rem;
        vertical-align: middle;
        background: rgba(255, 255, 255, 0.86);
    }

    .table-modern tbody tr:hover {
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.1);
    }

    .table-modern tbody tr td:first-child {
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }

    .table-modern tbody tr td:last-child {
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .empty-state {
        text-align: center;
        padding: 1.4rem;
        color: #94a3b8;
        font-weight: 600;
    }

    .keep-table td,
    .keep-table th {
        border: none;
        font-size: 0.86rem;
    }

    .keep-table tbody tr td {
        background: rgba(255, 255, 255, 0.88);
    }

    .cell-title {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.2rem;
    }

    .cell-meta {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.74rem;
        font-weight: 600;
        color: #64748b;
    }

    .cell-rank {
        margin-top: 0.35rem;
    }

    .nav-pills .nav-link {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }
    
    .nav-pills .nav-link-info { color: #0891b2; background: #cffafe; border-color: #a5f3fc; }
    .nav-pills .nav-link-info.active { color: #fff; background: #06b6d4; border-color: #06b6d4; box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3); }

    .nav-pills .nav-link-danger { color: #e11d48; background: #ffe4e6; border-color: #fecdd3; }
    .nav-pills .nav-link-danger.active { color: #fff; background: #f43f5e; border-color: #f43f5e; box-shadow: 0 4px 12px rgba(244, 63, 94, 0.3); }

    .nav-pills .nav-link-warning { color: #ca8a04; background: #fef9c3; border-color: #fde047; }
    .nav-pills .nav-link-warning.active { color: #fff; background: #eab308; border-color: #eab308; box-shadow: 0 4px 12px rgba(234, 179, 8, 0.3); }

    .nav-pills .nav-link-success { color: #059669; background: #d1fae5; border-color: #a7f3d0; }
    .nav-pills .nav-link-success.active { color: #fff; background: #10b981; border-color: #10b981; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }

    .nav-pills .nav-link-secondary { color: #475569; background: #f1f5f9; border-color: #e2e8f0; }
    .nav-pills .nav-link-secondary.active { color: #fff; background: #64748b; border-color: #64748b; box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3); }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="page-headline mb-1">Aspirasi Masuk</h3>
        <p class="page-subtitle small mb-0">Kelola antrean aspirasi berdasarkan status, prioritas, dan lock level.</p>
    </div>
    <div class="d-flex gap-2 page-actions">
        <a href="{{ route('admin.aspirations.export', request()->all()) }}" class="btn btn-success btn-custom">
            <i class="fas fa-file-excel me-2"></i> Export Excel
        </a>
        <a href="{{ route('admin.aspirations.export.pdf', request()->all()) }}" class="btn btn-danger btn-custom">
            <i class="fas fa-file-pdf me-2"></i> Export PDF
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex flex-column">
                <div class="stat-card-icon mb-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #22c55e, #10b981); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 8px 16px rgba(34, 197, 94, 0.2);">
                    <i class="fas fa-check-double text-sm"></i>
                </div>
                <p class="text-label mb-1" style="font-size: 0.65rem;">Aspirasi Selesai</p>
                <h4 class="fw-black mb-0" style="color: #1f2759;">{{ $summary['selesai'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex flex-column">
                <div class="stat-card-icon mb-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #ef4444, #f43f5e); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 8px 16px rgba(239, 68, 68, 0.2);">
                    <i class="fas fa-bolt text-sm"></i>
                </div>
                <p class="text-label mb-1" style="font-size: 0.65rem;">High Priority</p>
                <h4 class="fw-black mb-0" style="color: #1f2759;">{{ $summary['highPriorityCustom'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex flex-column">
                <div class="stat-card-icon mb-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #f59e0b, #fbbf24); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 8px 16px rgba(245, 158, 11, 0.2);">
                    <i class="fas fa-hourglass-half text-sm"></i>
                </div>
                <p class="text-label mb-1" style="font-size: 0.65rem;">Pending Template</p>
                <h4 class="fw-black mb-0" style="color: #1f2759;">{{ $summary['menungguTemplate'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex flex-column">
                <div class="stat-card-icon mb-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #7a5af8, #5b8dff); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 8px 16px rgba(122, 90, 248, 0.2);">
                    <i class="fas fa-inbox text-sm"></i>
                </div>
                <p class="text-label mb-1" style="font-size: 0.65rem;">Total Aspirasi</p>
                <h4 class="fw-black mb-0" style="color: #1f2759;">{{ $aspirations->total() }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-5">
    <div class="col-12 col-md-4">
        <div class="stat-card" style="border: 1px solid #e2e8f0;">
            <div class="d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-label mb-0" style="font-size: 0.75rem;">Fast Respond (&lt; 7 Hari)</p>
                    <div class="stat-card-icon" style="width: 32px; height: 32px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);">
                        <i class="fas fa-rocket text-xs"></i>
                    </div>
                </div>
                <h3 class="fw-black mb-0" style="color: #1f2759;">{{ $summary['fastRespond'] }} <span style="font-size: 0.8rem; font-weight: 500; color: #64748b;">aspirasi</span></h3>
                <div class="mt-2">
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1" style="font-size: 0.65rem; font-weight: 700;">
                        {{ $summary['fastRespondPercent'] }}%
                    </span>
                    <span class="text-secondary ms-1" style="font-size: 0.65rem;">dari total selesai</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card" style="border: 1px solid #e2e8f0;">
            <div class="d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-label mb-0" style="font-size: 0.75rem;">Normal (7 - 14 Hari)</p>
                    <div class="stat-card-icon" style="width: 32px; height: 32px; background: linear-gradient(135deg, #eab308, #ca8a04); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 4px 10px rgba(234, 179, 8, 0.2);">
                        <i class="fas fa-check text-xs"></i>
                    </div>
                </div>
                <h3 class="fw-black mb-0" style="color: #1f2759;">{{ $summary['normalRespond'] }} <span style="font-size: 0.8rem; font-weight: 500; color: #64748b;">aspirasi</span></h3>
                <div class="mt-2">
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1" style="font-size: 0.65rem; font-weight: 700;">
                        {{ $summary['normalRespondPercent'] }}%
                    </span>
                    <span class="text-secondary ms-1" style="font-size: 0.65rem;">dari total selesai</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card" style="border: 1px solid #e2e8f0;">
            <div class="d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-label mb-0" style="font-size: 0.75rem;">Slow Respond (&gt; 14 Hari)</p>
                    <div class="stat-card-icon" style="width: 32px; height: 32px; background: linear-gradient(135deg, #f43f5e, #e11d48); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 4px 10px rgba(244, 63, 94, 0.2);">
                        <i class="fas fa-hourglass-end text-xs"></i>
                    </div>
                </div>
                <h3 class="fw-black mb-0" style="color: #1f2759;">{{ $summary['slowRespond'] }} <span style="font-size: 0.8rem; font-weight: 500; color: #64748b;">aspirasi</span></h3>
                <div class="mt-2">
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1" style="font-size: 0.65rem; font-weight: 700;">
                        {{ $summary['slowRespondPercent'] }}%
                    </span>
                    <span class="text-secondary ms-1" style="font-size: 0.65rem;">dari total selesai</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="filter-panel mb-4">
    <p class="filter-title">Kolom Filter</p>
    <form action="{{ route('admin.aspirations.index') }}" method="GET" class="row g-2">
        <div class="col-md-3">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari lokasi/deskripsi...">
        </div>
        <div class="col-md-2">
            <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm" title="Tanggal awal">
        </div>
        <div class="col-md-2">
            <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm" title="Tanggal akhir">
        </div>
        <div class="col-md-2">
            <select name="category" class="form-select form-select-sm">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id_category }}" {{ request('category') == $cat->id_category ? 'selected' : '' }}>
                    {{ $cat->category_name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">Status Pengajuan</option>
                <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="mode" class="form-select form-select-sm">
                <option value="">Mode Aspirasi</option>
                <option value="template" {{ request('mode') == 'template' ? 'selected' : '' }}>Template</option>
                <option value="custom" {{ request('mode') == 'custom' ? 'selected' : '' }}>Custom</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="progress" class="form-select form-select-sm">
                <option value="">Progress Perbaikan</option>
                <option value="Belum Dimulai" {{ request('progress') == 'Belum Dimulai' ? 'selected' : '' }}>Belum Dimulai</option>
                <option value="Dalam Proses" {{ request('progress') == 'Dalam Proses' ? 'selected' : '' }}>Dalam Proses</option>
                <option value="Selesai" {{ request('progress') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <div class="col-md-1">
            <select name="sort" class="form-select form-select-sm">
                <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Terbaru</option>
                <option value="priority" {{ request('sort') == 'priority' ? 'selected' : '' }}>Prioritas</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
            <a href="{{ route('admin.aspirations.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">Reset</a>
        </div>
    </form>
</div>

@php
    $aspirationItems = $aspirations->getCollection();
    $groupSelesai = $aspirationItems->filter(fn($it) => $it->submission_status === 'diterima' && optional($it->aspiration)->progress_status === 'Selesai');
    $groupHighPriority = $aspirationItems->filter(fn($it) =>
        ($it->submission_mode ?? 'template') === 'custom'
           && $it->submission_status === 'menunggu'
           && !((bool) $it->is_kept && filled($it->kept_until) && $it->kept_until->isFuture())
    );
    $groupDiproses = $aspirationItems->filter(fn($it) =>
        $it->submission_status === 'diterima' && 
        optional($it->aspiration)->progress_status !== 'Selesai'
    );
    $groupMenungguTemplate = $aspirationItems->filter(fn($it) =>
        ($it->submission_mode ?? 'template') === 'template'
           && $it->submission_status === 'menunggu'
    );

    $groupHighPriority = $groupHighPriority->sortBy(function ($it) {
        return optional($it->created_at)->timestamp ?? 0;
    })->values();

    $groupMenungguTemplate = $groupMenungguTemplate->sortBy(function ($it) {
        return optional($it->created_at)->timestamp ?? 0;
    })->values();

    $lockedTemplateCount = $aspirationItems->filter(fn($it) =>
        ($it->submission_mode ?? 'template') === 'template'
        && $it->submission_status === 'menunggu'
        && ($templateLockedMap[$it->id_input] ?? false)
    )->count();

    $lockedCustomCount = $aspirationItems->filter(fn($it) =>
        ($it->submission_mode ?? 'template') === 'custom'
        && $it->submission_status === 'menunggu'
        && !((bool) $it->is_kept && filled($it->kept_until) && $it->kept_until->isFuture())
        && ($customLockedMap[$it->id_input] ?? false)
    )->count();

    $diprosesSection = ['id' => 'diproses', 'title' => 'Sedang Diproses', 'count' => $groupDiproses->count(), 'items' => $groupDiproses, 'borderClass' => 'border-info', 'textClass' => 'text-info', 'lockedCount' => 0, 'icon' => 'fa-spinner', 'tone' => 'info'];
    $customSection = ['id' => 'custom', 'title' => 'High Priority (Custom)', 'count' => $groupHighPriority->count(), 'items' => $groupHighPriority, 'borderClass' => 'border-danger', 'textClass' => 'text-danger', 'lockedCount' => $lockedCustomCount, 'icon' => 'fa-bolt', 'tone' => 'danger'];
    $selesaiSection = ['id' => 'selesai', 'title' => 'Aspirasi Selesai', 'count' => $groupSelesai->count(), 'items' => $groupSelesai, 'borderClass' => 'border-success', 'textClass' => 'text-success', 'lockedCount' => 0, 'icon' => 'fa-circle-check', 'tone' => 'success'];
    $templateSection = ['id' => 'menunggu', 'title' => 'Aspirasi Template (Menunggu)', 'count' => $groupMenungguTemplate->count(), 'items' => $groupMenungguTemplate, 'borderClass' => 'border-warning', 'textClass' => 'text-warning', 'lockedCount' => $lockedTemplateCount, 'icon' => 'fa-list-check', 'tone' => 'warning'];
@endphp

<div class="nav-pills-custom-wrapper mb-4">
    <ul class="nav nav-pills d-flex flex-wrap gap-2" id="aspirationTab" role="tablist">
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link nav-link-danger w-100 rounded-pill active fw-bold py-2 d-flex align-items-center justify-content-center" id="custom-tab" data-bs-toggle="tab" data-bs-target="#custom-pane" type="button" role="tab" aria-controls="custom-pane" aria-selected="true">
                <i class="fas fa-bolt me-2"></i> High Priority <span class="badge bg-white text-danger ms-2 rounded-pill">{{ $groupHighPriority->count() }}</span>
            </button>
        </li>
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link nav-link-warning w-100 rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center" id="template-tab" data-bs-toggle="tab" data-bs-target="#template-pane" type="button" role="tab" aria-controls="template-pane" aria-selected="false">
                <i class="fas fa-list-check me-2"></i> Template <span class="badge bg-white text-warning ms-2 rounded-pill">{{ $groupMenungguTemplate->count() }}</span>
            </button>
        </li>
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link nav-link-info w-100 rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center" id="diproses-tab" data-bs-toggle="tab" data-bs-target="#diproses-pane" type="button" role="tab" aria-controls="diproses-pane" aria-selected="false">
                <i class="fas fa-spinner me-2"></i> Sedang Diproses <span class="badge bg-white text-info ms-2 rounded-pill">{{ $groupDiproses->count() }}</span>
            </button>
        </li>
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link nav-link-success w-100 rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center" id="selesai-tab" data-bs-toggle="tab" data-bs-target="#selesai-pane" type="button" role="tab" aria-controls="selesai-pane" aria-selected="false">
                <i class="fas fa-circle-check me-2"></i> Selesai <span class="badge bg-white text-success ms-2 rounded-pill">{{ $groupSelesai->count() }}</span>
            </button>
        </li>
        <li class="nav-item flex-fill" role="presentation">
            <button class="nav-link nav-link-secondary w-100 rounded-pill fw-bold py-2 d-flex align-items-center justify-content-center" id="keep-tab" data-bs-toggle="tab" data-bs-target="#keep-pane" type="button" role="tab" aria-controls="keep-pane" aria-selected="false">
                <i class="fas fa-clock me-2"></i> Di-Keep <span class="badge bg-white text-secondary ms-2 rounded-pill">{{ $activeKeptCustom->count() }}</span>
            </button>
        </li>
    </ul>
</div>

<div class="tab-content mb-4" id="aspirationTabContent">
    <div class="tab-pane fade show active" id="custom-pane" role="tabpanel" tabindex="0">
        @include('admin.aspirations.partials.card', ['section' => $customSection])
    </div>
    <div class="tab-pane fade" id="template-pane" role="tabpanel" tabindex="0">
        @include('admin.aspirations.partials.card', ['section' => $templateSection])
    </div>
    <div class="tab-pane fade" id="diproses-pane" role="tabpanel" tabindex="0">
        @include('admin.aspirations.partials.card', ['section' => $diprosesSection])
    </div>
    <div class="tab-pane fade" id="selesai-pane" role="tabpanel" tabindex="0">
        @include('admin.aspirations.partials.card', ['section' => $selesaiSection])
    </div>
    <div class="tab-pane fade" id="keep-pane" role="tabpanel" tabindex="0">
        <div class="asp-card tone-info">
            <div class="asp-card-header">
                <div>
                    <div class="asp-head-main">
                        <span class="asp-icon info"><i class="fas fa-clock"></i></span>
                        <p class="asp-card-title text-info">Keep Aspirasi (3 Hari)</p>
                    </div>
                    <span class="asp-card-count mt-2"><i class="fas fa-layer-group"></i> {{ $activeKeptCustom->count() }} aspirasi sedang di-keep</span>
                </div>
            </div>
            <div class="p-3">
                @if($activeKeptCustom->isEmpty())
                    <p class="mb-0 text-secondary small">Belum ada aspirasi high priority yang sedang di-keep.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0 keep-table">
                            <thead>
                                <tr class="text-label">
                                    <th>ID</th>
                                    <th>Siswa</th>
                                    <th>Judul</th>
                                    <th>Batas Keep</th>
                                    <th>Catatan</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeKeptCustom as $keepItem)
                                    <tr>
                                        <td>#{{ $keepItem->id_input }}</td>
                                        <td>{{ $keepItem->student->username ?? '-' }}</td>
                                        <td>{{ $keepItem->title }}</td>
                                        <td class="fw-bold text-info">{{ optional($keepItem->kept_until)->format('d M Y H:i') }}</td>
                                        <td>{{ $keepItem->kept_note ?: '-' }}</td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.aspirations.show', $keepItem->id_input) }}" class="btn btn-outline-primary btn-sm rounded-3 px-3">Detail</a>
                                                <form action="{{ route('admin.aspirations.keep.release', $keepItem->id_input) }}" method="POST">
                                                    @csrf
                                                    <button class="btn btn-outline-warning btn-sm rounded-3 px-3">Lepas Keep</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var triggerTabList = [].slice.call(document.querySelectorAll('#aspirationTab button[data-bs-toggle="tab"]'))
        triggerTabList.forEach(function (triggerEl) {
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                // Remove active class from all tabs
                triggerTabList.forEach(function(el) {
                    el.classList.remove('active');
                    el.setAttribute('aria-selected', 'false');
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                
                // Hide all tab panes
                var tabPanes = document.querySelectorAll('.tab-content .tab-pane');
                tabPanes.forEach(function(pane) {
                    pane.classList.remove('show', 'active');
                });
                
                // Show target pane
                var targetPane = document.querySelector(this.getAttribute('data-bs-target'));
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            })
        })
    });
</script>

<div class="mt-4 d-flex justify-content-center">
    {{ $aspirations->links() }}
</div>

<script>
    function toggleAspSection(button) {
        const card = button.closest('.asp-card');
        const targetId = button.getAttribute('data-target');
        const section = document.getElementById(targetId);
        if (!section || !card) return;

        const collapsed = card.classList.toggle('is-collapsed');
        button.innerHTML = collapsed
            ? '<i class="fas fa-chevron-down me-1"></i> Buka'
            : '<i class="fas fa-chevron-up me-1"></i> Tutup';
    }
</script>
@endsection
