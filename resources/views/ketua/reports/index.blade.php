@extends('layout.dashboard')
@section('title', 'Laporan Tahunan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-white mb-1">Laporan Tahunan {{ $year }}</h4>
        <p class="text-secondary small mb-0">Ringkasan kinerja penanganan aspirasi siswa.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        {{-- Year Selector --}}
        <form action="{{ route('ketua.reports.index') }}" method="GET" class="d-flex gap-2">
            <select name="year" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
                @foreach($availableYears as $y)
                <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('ketua.reports.export-pdf', ['year' => $year]) }}" class="btn btn-danger btn-sm px-3 fw-bold rounded-pill shadow-sm">
            <i class="fas fa-file-pdf me-1"></i> Export PDF
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card border-0 shadow-lg text-center" style="background: #1a1d21;">
            <div class="h2 fw-bold text-white mb-1">{{ $stats['totalMasuk'] }}</div>
            <p class="text-label mb-0 text-primary">Total Aspirasi</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-0 shadow-lg text-center" style="background: #1a1d21;">
            <div class="h2 fw-bold text-success mb-1">{{ $stats['diterima'] }}</div>
            <p class="text-label mb-0 text-success">Disetujui</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-0 shadow-lg text-center" style="background: #1a1d21;">
            <div class="h2 fw-bold text-danger mb-1">{{ $stats['ditolak'] }}</div>
            <p class="text-label mb-0 text-danger">Ditolak</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card border-0 shadow-lg text-center" style="background: #1a1d21;">
            <div class="h2 fw-bold text-info mb-1">{{ $stats['selesai'] }}</div>
            <p class="text-label mb-0 text-info">Tuntas</p>
        </div>
    </div>
</div>

{{-- Rate Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card border-0 shadow-lg" style="background: #1a1d21;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(40, 167, 69, 0.1);">
                    <i class="fas fa-percentage text-success fa-lg"></i>
                </div>
                <div>
                    <p class="text-label mb-0 text-secondary">Tingkat Persetujuan</p>
                    <h4 class="fw-bold text-white mb-0">{{ $stats['approvalRate'] }}%</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card border-0 shadow-lg" style="background: #1a1d21;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(52, 152, 219, 0.1);">
                    <i class="fas fa-check-double text-info fa-lg"></i>
                </div>
                <div>
                    <p class="text-label mb-0 text-secondary">Tingkat Penyelesaian</p>
                    <h4 class="fw-bold text-white mb-0">{{ $stats['completionRate'] }}%</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card border-0 shadow-lg" style="background: #1a1d21;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.1);">
                    <i class="fas fa-star text-warning fa-lg"></i>
                </div>
                <div>
                    <p class="text-label mb-0 text-secondary">Kepuasan Siswa</p>
                    <h4 class="fw-bold text-white mb-0">
                        {{ $stats['avgRating'] ? number_format($stats['avgRating'], 1) : '-' }}<span class="text-secondary fs-6">/5</span>
                        <span class="text-secondary small ms-1">({{ $stats['totalRated'] }} rating)</span>
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="stat-card border-0 shadow-lg p-4" style="background: #1a1d21;">
            <h6 class="fw-bold text-white mb-4">Tren Aspirasi Bulanan — {{ $year }}</h6>
            <div style="height: 300px;">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="stat-card border-0 shadow-lg p-4" style="background: #1a1d21;">
            <h6 class="fw-bold text-white mb-4">Sebaran Kategori</h6>
            <div style="height: 300px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Data bridge --}}
<div id="report-data"
    data-monthly-labels='@json($monthlyData["labels"])'
    data-monthly-values='@json($monthlyData["values"])'
    data-category-labels='@json($categoryData["labels"])'
    data-category-values='@json($categoryData["values"])'>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dataEl = document.getElementById('report-data');
    if (!dataEl) return;

    const textColor = '#94a3b8';
    const gridColor = 'rgba(255,255,255,0.05)';

    const mLabels = JSON.parse(dataEl.dataset.monthlyLabels || '[]');
    const mValues = JSON.parse(dataEl.dataset.monthlyValues || '[]');
    const cLabels = JSON.parse(dataEl.dataset.categoryLabels || '[]');
    const cValues = JSON.parse(dataEl.dataset.categoryValues || '[]');

    // Monthly trend bar chart
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: mLabels,
            datasets: [{
                label: 'Aspirasi Masuk',
                data: mValues,
                backgroundColor: 'rgba(61, 90, 254, 0.6)',
                borderColor: '#3d5afe',
                borderWidth: 1,
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: textColor, stepSize: 1 },
                    grid: { color: gridColor }
                },
                x: {
                    ticks: { color: textColor },
                    grid: { display: false }
                }
            }
        }
    });

    // Category doughnut
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: cLabels.length ? cLabels : ['Tidak ada data'],
            datasets: [{
                data: cValues.length ? cValues : [1],
                backgroundColor: ['#3d5afe', '#f1c40f', '#e74c3c', '#2ecc71', '#870cda', '#f97316', '#06b6d4'],
                borderWidth: 0,
                hoverOffset: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: textColor, usePointStyle: true, padding: 15, font: { size: 11 } }
                }
            }
        }
    });
});
</script>
@endpush
