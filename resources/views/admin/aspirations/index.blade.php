@extends('layout.dashboard')
@section('title', 'Aspirasi Siswa')

@section('content')
<style>
    .animate-pulse-red {
        animation: pulse-red 2s infinite;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }

    .vote-badge {
        background: rgba(13, 110, 253, 0.1);
        border: 1px solid rgba(13, 110, 253, 0.2);
        color: #0d6efd;
        font-weight: 800;
        padding: 0.4rem 0.7rem;
    }

    .priority-text {
        font-size: 0.65rem;
        letter-spacing: 1px;
        font-weight: 900;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-white mb-1">Aspirasi Masuk</h3>
        <p class="text-secondary small">Urutan berdasarkan laporan terbaru dan dukungan siswa.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.aspirations.export', request()->all()) }}" class="btn btn-success btn-custom">
            <i class="fas fa-file-excel me-2"></i> Export Excel
        </a>
        <a href="{{ route('admin.aspirations.export.pdf', request()->all()) }}" class="btn btn-danger btn-custom">
            <i class="fas fa-file-pdf me-2"></i> Export PDF
        </a>
    </div>
</div>

{{-- Filter Box --}}
<div class="stat-card mb-4">
    <form action="{{ route('admin.aspirations.index') }}" method="GET" class="row g-2">
        <div class="col-md-3">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari lokasi/deskripsi...">
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
                <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
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
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
            <a href="{{ route('admin.aspirations.index') }}" class="btn btn-outline-secondary btn-sm flex-fill text-white">Reset</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="stat-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle" style="border-color: #1f222d;">
            <thead style="background: rgba(255,255,255,0.02);">
                <tr class="text-label">
                    <th class="ps-4">ID</th>
                    <th>Prioritas Dukungan</th>
                    <th>Siswa</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Tanggal</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-secondary" style="font-size: 0.9rem;">
                @forelse ($aspirations as $item)
                <tr>
                    {{-- $item adalah InputAspirations, jadi langsung akses id_input --}}
                    <td class="ps-4">#{{ $item->id_input }}</td>

                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="vote-badge rounded-3 d-flex align-items-center">
                                <i class="fas fa-heart me-2 {{ ($item->aspiration->votes_count ?? 0) > 0 ? 'text-danger' : 'opacity-25' }}"></i>
                                <span>{{ $item->aspiration->votes_count ?? 0 }}</span>
                            </div>

                            @if(($item->aspiration->votes_count ?? 0) >= 10)
                                <span class="badge bg-danger rounded-pill priority-text animate-pulse-red px-2 py-1">HIGH PRIORITY</span>
                            @elseif(($item->aspiration->votes_count ?? 0) >= 5)
                                <span class="badge bg-warning text-dark rounded-pill priority-text px-2 py-1">NEED ATTENTION</span>
                            @endif
                        </div>
                    </td>

                    <td class="text-white fw-medium">{{ $item->student->username ?? '-' }}</td>
                    <td>{{ $item->category->category_name ?? '-' }}</td>
                    {{-- Lokasi langsung dari $item --}}
                    <td><i class="fas fa-map-marker-alt me-1 opacity-50"></i> {{ $item->location ?? '-' }}</td>

                    <td>
                        @php
                            $status = $item->submission_status ?? 'menunggu';
                            $s_color = match($status) {
                                'menunggu' => 'bg-secondary',
                                'reviewed' => 'bg-warning text-dark',
                                'diterima' => 'bg-success',
                                'ditolak' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $s_color }} rounded-pill" style="font-size: 0.7rem;">
                            {{ strtoupper($status) }}
                        </span>
                    </td>

                    <td>
                        @if ($item->submission_status === 'diterima' && $item->aspiration)
                            @php
                                $p_status = $item->aspiration->progress_status;
                                $p_color = match($p_status) {
                                    'Belum Dimulai' => 'text-secondary',
                                    'Dalam Proses' => 'text-info',
                                    'Selesai' => 'text-success fw-bold',
                                    default => 'text-secondary'
                                };
                            @endphp
                            <span class="{{ $p_color }}" style="font-size: 0.85rem;">
                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> {{ $p_status }}
                            </span>
                        @else
                            <span class="opacity-25">-</span>
                        @endif
                    </td>

                    <td>{{ $item->created_at->format('d/m/y') }}</td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.aspirations.show', $item->id_input) }}" class="btn btn-outline-primary btn-sm rounded-3 px-3 shadow-sm hover-up">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 opacity-50 text-white">
                        <i class="fas fa-inbox d-block mb-2 fs-2"></i>
                        Tidak ada data ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 d-flex justify-content-center">
    {{ $aspirations->links() }}
</div>
@endsection