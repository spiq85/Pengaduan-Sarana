@extends('layout.dashboard')
@section('title', 'Dashboard Ketua')

@section('content')
<div class="mb-5">
    <h4 class="fw-bold text-white mb-1">Executive Dashboard</h4>
    <p class="text-secondary small">Ringkasan persetujuan dan sebaran kategori aspirasi.</p>
</div>

{{-- Row Stat Cards --}}
<div class="row g-4">
    <div class="col-md-4 col-lg">
        <div class="stat-card border-0 border-start border-warning border-4 shadow-lg" style="background: rgba(255, 193, 7, 0.03);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-label mb-1 text-warning">Perlu Direview</p>
                    <h2 class="fw-bold text-white mb-0">{{ $stats['status']['review'] ?? 0 }}</h2>
                </div>
                <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                    <i class="fas fa-clock fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg">
        <div class="stat-card border-0 border-start border-success border-4 shadow-lg" style="background: rgba(40, 167, 69, 0.03);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-label mb-1 text-success">Aspirasi Disetujui</p>
                    <h2 class="fw-bold text-white mb-0">{{ $stats['status']['diterima'] ?? 0 }}</h2>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-lg">
        <div class="stat-card border-0 border-start border-danger border-4 shadow-lg" style="background: rgba(220, 53, 69, 0.03);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-label mb-1 text-danger">Aspirasi Ditolak</p>
                    <h2 class="fw-bold text-white mb-0">{{ $stats['status']['ditolak'] ?? 0 }}</h2>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger">
                    <i class="fas fa-times-circle fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg">
        <div class="stat-card border-0 border-start border-info border-4 shadow-lg" style="background: rgba(52, 152, 219, 0.03);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-label mb-1 text-info">Dalam Proses</p>
                    <h2 class="fw-bold text-white mb-0">{{ $stats['progress']['proses'] ?? 0 }}</h2>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info">
                    <i class="fas fa-cog fa-spin fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg">
        <div class="stat-card border-0 border-start border-4 shadow-lg" style="background: rgba(46, 204, 113, 0.03); border-color: #2ecc71 !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-label mb-1" style="color: #2ecc71;">Selesai / Tuntas</p>
                    <h2 class="fw-bold text-white mb-0">{{ $stats['progress']['selesai'] ?? 0 }}</h2>
                </div>
                <div class="p-3 rounded-3" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71;">
                    <i class="fas fa-check-double fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Row Charts --}}
<div class="row g-4 mt-2">
    {{-- Left: Proporsi Status (Doughnut) --}}
    <div class="col-lg-5">
        <div class="stat-card border-0 shadow-lg h-100" style="background: #1a1d21;">
            <h6 class="fw-bold text-white mb-4">Efektivitas Persetujuan</h6>
            <div style="height: 300px; position: relative;">
                {{-- Data Bridge untuk Status --}}
                <div id="status-data"
                    data-menunggu="{{ $stats['status']['review'] }}"
                    data-diterima="{{ $stats['status']['diterima'] }}"
                    data-ditolak="{{ $stats['status']['ditolak'] }}">
                </div>
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Right: Top Kategori (Horizontal Bar) --}}
    <div class="col-lg-7">
        <div class="stat-card border-0 shadow-lg h-100" style="background: #1a1d21;">
            <h6 class="fw-bold text-white mb-4">Sebaran Aspirasi Per Kategori</h6>
            <div style="height: 300px; position: relative;">
                {{-- Data Bridge untuk Kategori --}}
                <div id="category-data"
                    data-labels="{{ json_encode($charts['categories']['labels'] ?? []) }}"
                    data-values="{{ json_encode($charts['categories']['values'] ?? []) }}">
                </div>
                <canvas id="categoryBarChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. PROPORSI STATUS CHART ---
        const statusEl = document.getElementById('status-data');
        if (statusEl) {
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Menunggu', 'Disetujui', 'Ditolak'],
                    datasets: [{
                        data: [
                            statusEl.dataset.menunggu,
                            statusEl.dataset.diterima,
                            statusEl.dataset.ditolak
                        ],
                        backgroundColor: ['#f1c40f', '#2ecc71', '#e74c3c'],
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#94a3b8',
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    }
                }
            });
        }

        // --- 2. KATEGORI CHART (Horizontal Bar) ---
        const catEl = document.getElementById('category-data');
        if (catEl) {
            const labels = JSON.parse(catEl.getAttribute('data-labels') || '[]');
            const values = JSON.parse(catEl.getAttribute('data-values') || '[]');

            new Chart(document.getElementById('categoryBarChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Aspirasi',
                        data: values,
                        backgroundColor: 'rgba(52, 152, 219, 0.6)',
                        borderColor: '#3498db',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y', // Ini yang bikin Bar jadi Horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(255,255,255,0.05)'
                            },
                            ticks: {
                                color: '#94a3b8'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#94a3b8'
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush