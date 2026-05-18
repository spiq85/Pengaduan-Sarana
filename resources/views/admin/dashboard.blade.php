@extends('layout.dashboard')
@section('title', 'System Dashboard')

@section('content')
<style>
    .headline-title {
        background: linear-gradient(120deg, #6c4ef6 0%, #4f7eff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .metric-card {
        position: relative;
        overflow: hidden;
    }

    .metric-card::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -30px;
        width: 120px;
        height: 120px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(122, 90, 248, 0.2) 0%, rgba(122, 90, 248, 0) 70%);
    }

    .chip-filter {
        background: #f7f9ff;
        border: 1px solid #dae5ff;
        color: #5e6a9c;
    }
</style>

<div class="d-flex justify-content-between align-items-end mb-5">
    <div>
        <h2 class="fw-bold mb-1 headline-title">System Overview</h2>
        <p class="text-secondary mb-0">Laporan statistik sarana dan prasarana sekolah.</p>
    </div>
    <div class="text-secondary small">
        <i class="fas fa-sync-alt me-1"></i> Terakhir diperbarui: {{ now()->format('H:i') }}
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="stat-card metric-card" style="border-bottom: 3px solid #7a5af8; background: linear-gradient(160deg, #ffffff 0%, #f8f4ff 100%);">
            <div class="text-label mb-2" style="color:#7562be;">Total Aspirasi</div>
            <div class="h1 fw-bold mb-0 text-white">{{ $stats['total_masuk'] }}</div>
            <div class="mt-3 small text-secondary">Data akumulasi masuk</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card metric-card" style="border-bottom: 3px solid #5b8dff; background: linear-gradient(160deg, #ffffff 0%, #f2f7ff 100%);">
            <div class="text-label mb-2" style="color:#6682c8;">Menunggu Review</div>
            <div class="h1 fw-bold mb-0 text-white">{{ $stats['status']['menunggu'] }}</div>
            <div class="mt-3 small text-secondary">Perlu verifikasi admin</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card metric-card" style="border-bottom: 3px solid #9c73f7; background: linear-gradient(160deg, #ffffff 0%, #faf6ff 100%);">
            <div class="text-label mb-2" style="color:#8d70c4;">Aspirasi Ditolak</div>
            <div class="h1 fw-bold mb-0 text-white">{{ $stats['status']['ditolak'] }}</div>
            <div class="mt-3 small text-secondary">Tidak memenuhi kriteria</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card metric-card" style="border-bottom: 3px solid #7a5af8; background: linear-gradient(160deg, #ffffff 0%, #f8f3ff 100%);">
            <div class="text-label mb-2" style="color:#7a66bc;">Dalam Proses</div>
            <div class="h1 fw-bold mb-0 text-white">{{ $stats['progress']['proses'] }}</div>
            <div class="mt-3 small text-secondary">Sedang ditindaklanjuti</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card metric-card" style="border-bottom: 3px solid #4f7eff; background: linear-gradient(160deg, #ffffff 0%, #f2f7ff 100%);">
            <div class="text-label mb-2" style="color:#647fc7;">Selesai / Tuntas</div>
            <div class="h1 fw-bold mb-0 text-white">{{ $stats['progress']['selesai'] }}</div>
            <div class="mt-3 small text-secondary">Perbaikan telah selesai</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card metric-card" style="border-bottom: 3px solid #6d56f2; background: linear-gradient(160deg, #ffffff 0%, #f5f3ff 100%);">
            <div class="text-label mb-2" style="color:#7664c0;">Disetujui Admin</div>
            <div class="h1 fw-bold mb-0 text-white">{{ $stats['status']['diterima'] }}</div>
            <div class="mt-3 small text-secondary">Sedang/siap ditindaklanjuti</div>
        </div>
    </div>
</div>

<div class="mt-5">
    <div class="stat-card p-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(120deg, #ffffff 0%, #f2f7ff 100%);">
        <div>
            <h5 class="mb-1 fw-bold text-white">Manajemen Data</h5>
            <p class="text-secondary small mb-0">Klik tombol di samping untuk masuk ke modul detail.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.aspirations.index') }}" class="btn btn-primary btn-custom shadow-sm">
                Buka Aspirasi Siswa
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-custom">
                Kelola Kategori
            </a>
        </div>
    </div>
</div>

{{-- ===================== CHART SECTION ===================== --}}
<div class="row mt-5 g-4">
    <div class="col-lg-8">
        <div class="stat-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0 text-white">Aspirasi Masuk</h6>
                <div class="btn-group btn-group-sm" role="group">
                    <a href="?trend=daily" class="btn {{ ($trendFilter ?? 'daily') === 'daily' ? 'btn-primary' : 'btn-outline-secondary chip-filter' }} btn-sm rounded-start-pill px-3" style="font-size: 11px;">Harian</a>
                    <a href="?trend=weekly" class="btn {{ ($trendFilter ?? 'daily') === 'weekly' ? 'btn-primary' : 'btn-outline-secondary chip-filter' }} btn-sm px-3" style="font-size: 11px;">Mingguan</a>
                    <a href="?trend=monthly" class="btn {{ ($trendFilter ?? 'daily') === 'monthly' ? 'btn-primary' : 'btn-outline-secondary chip-filter' }} btn-sm px-3" style="font-size: 11px;">Bulanan</a>
                    <a href="?trend=yearly" class="btn {{ ($trendFilter ?? 'daily') === 'yearly' ? 'btn-primary' : 'btn-outline-secondary chip-filter' }} btn-sm rounded-end-pill px-3" style="font-size: 11px;">Tahunan</a>
                </div>
            </div>
            <div style="height:300px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="stat-card p-4">
            <h6 class="fw-bold mb-4 text-white">Top Kategori</h6>
            <div style="height:300px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- ===================== SATISFACTION CHART ===================== --}}
<div class="row mt-4 g-4">
    <div class="col-lg-5">
        <div class="stat-card p-4 h-100">
            <h6 class="fw-bold mb-3 text-white">Kepuasan Siswa</h6>
            <div class="text-center mb-3">
                <div class="d-inline-flex align-items-baseline gap-2">
                    <span class="display-4 fw-bold text-white">{{ $charts['satisfaction']['average'] ?? 0 }}</span>
                    <span class="text-secondary">/5</span>
                </div>
                <div class="mt-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ ($charts['satisfaction']['average'] ?? 0) >= $i ? 'text-warning' : 'text-secondary opacity-25' }}" style="font-size: 18px;"></i>
                    @endfor
                </div>
                <p class="text-secondary small mt-2 mb-0">
                    dari <span class="text-white fw-bold">{{ $charts['satisfaction']['total_rated'] ?? 0 }}</span> penilaian siswa
                </p>
            </div>
            <div style="height: 180px;">
                <canvas id="satisfactionChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="stat-card p-4 h-100">
            <h6 class="fw-bold mb-3 text-white">Distribusi Rating</h6>
            <div class="mt-3">
                @php
                    $dist = $charts['satisfaction']['distribution'] ?? [];
                    $maxDist = max(array_values($dist) ?: [1]);
                @endphp
                @for($i = 5; $i >= 1; $i--)
                    @php
                        $count = $dist[$i] ?? 0;
                        $pct = $maxDist > 0 ? ($count / $maxDist) * 100 : 0;
                        $totalRated = $charts['satisfaction']['total_rated'] ?? 0;
                        $realPct = $totalRated > 0 ? round(($count / $totalRated) * 100) : 0;
                    @endphp
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="text-warning fw-bold" style="min-width: 28px; font-size: 13px;">{{ $i }} <i class="fas fa-star" style="font-size: 10px;"></i></span>
                        <div class="flex-grow-1 rounded-pill overflow-hidden" style="height: 10px; background: #e8efff;">
                            <div class="h-100 rounded-pill rating-fill" data-width="{{ $pct }}" style="width: 0; background: linear-gradient(90deg, #7a5af8, #5b8dff); transition: width 0.8s ease;"></div>
                        </div>
                        <span class="text-secondary small fw-bold" style="min-width: 55px; text-align: right;">{{ $count }} <span class="opacity-50">({{ $realPct }}%)</span></span>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>

{{-- Data Bridge --}}
<div id="chart-data"
    data-trend-labels='@json($charts["trend"]["labels"] ?? [])'
    data-trend-values='@json($charts["trend"]["values"] ?? [])'
    data-category-labels='@json($charts["categories"]["labels"] ?? [])'
    data-category-values='@json($charts["categories"]["values"] ?? [])'
    data-satisfaction-labels='@json($charts["satisfaction"]["labels"] ?? [])'
    data-satisfaction-values='@json($charts["satisfaction"]["values"] ?? [])'>
</div>

@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.rating-fill').forEach(function(el) {
            const pct = Number(el.dataset.width || 0);
            el.style.width = `${pct}%`;
        });

        const dataEl = document.getElementById('chart-data');
        if (!dataEl) return;

        const trendLabels = JSON.parse(dataEl.dataset.trendLabels || "[]");
        const trendValues = JSON.parse(dataEl.dataset.trendValues || "[]");
        const catLabels = JSON.parse(dataEl.dataset.categoryLabels || "[]");
        const catValues = JSON.parse(dataEl.dataset.categoryValues || "[]");

        const textColor = '#6b739f';
        const gridColor = 'rgba(123, 142, 226, 0.2)';

        // Trend Chart
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: trendLabels.length ? trendLabels : ['No Data'],
                datasets: [{
                    data: trendValues.length ? trendValues : [0],
                    borderColor: '#7a5af8',
                    backgroundColor: 'rgba(122,90,248,0.12)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: '#7a5af8'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: textColor
                        },
                        grid: {
                            color: gridColor
                        }
                    },
                    x: {
                        ticks: {
                            color: textColor
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Category Chart
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: catLabels.length ? catLabels : ['Empty'],
                datasets: [{
                    data: catValues.length ? catValues : [1],
                    backgroundColor: [
                        '#7a5af8',
                        '#5b8dff',
                        '#9f87ff',
                        '#8ec0ff',
                        '#6651e8'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: textColor,
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Satisfaction Chart (Doughnut)
        const satLabels = JSON.parse(dataEl.dataset.satisfactionLabels || "[]");
        const satValues = JSON.parse(dataEl.dataset.satisfactionValues || "[]");

        if (document.getElementById('satisfactionChart')) {
            new Chart(document.getElementById('satisfactionChart'), {
                type: 'doughnut',
                data: {
                    labels: satLabels.length ? satLabels : ['Belum ada'],
                    datasets: [{
                        data: satValues.length ? satValues : [1],
                        backgroundColor: [
                            '#ef4444',
                            '#f97316',
                            '#f59e0b',
                            '#22c55e',
                            '#10b981'
                        ],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                padding: 12,
                                usePointStyle: true,
                                font: { size: 11 }
                            }
                        }
                    }
                }
            });
        }

    });
</script>

@endpush