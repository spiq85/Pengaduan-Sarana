@extends('layout.dashboard')
@section('title', 'Aspirasi Masuk')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold text-white mb-1">Daftar Persetujuan</h4>
    <p class="text-secondary small">Tinjau dan berikan keputusan untuk aspirasi yang diteruskan oleh Admin.</p>
</div>

@if (session('success'))
    <div class="alert alert-success bg-success text-white border-0 shadow-sm mb-4">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    </div>
@endif

{{-- Filter Tabs --}}
<div class="d-flex gap-2 mb-4 flex-wrap">
    <a href="{{ route('ketua.aspirations.index', ['filter' => 'reviewed']) }}"
       class="btn btn-sm rounded-pill px-4 py-2 fw-bold {{ ($filter ?? 'reviewed') === 'reviewed' ? 'btn-warning text-dark' : 'btn-outline-secondary text-white' }}">
        <i class="fas fa-clock me-1"></i> Perlu Review
        @if(($counts['reviewed'] ?? 0) > 0)
            <span class="badge bg-dark ms-1">{{ $counts['reviewed'] }}</span>
        @endif
    </a>
    <a href="{{ route('ketua.aspirations.index', ['filter' => 'diterima']) }}"
       class="btn btn-sm rounded-pill px-4 py-2 fw-bold {{ ($filter ?? '') === 'diterima' ? 'btn-success' : 'btn-outline-secondary text-white' }}">
        <i class="fas fa-check-circle me-1"></i> Disetujui
        @if(($counts['diterima'] ?? 0) > 0)
            <span class="badge bg-dark ms-1">{{ $counts['diterima'] }}</span>
        @endif
    </a>
    <a href="{{ route('ketua.aspirations.index', ['filter' => 'ditolak']) }}"
       class="btn btn-sm rounded-pill px-4 py-2 fw-bold {{ ($filter ?? '') === 'ditolak' ? 'btn-danger' : 'btn-outline-secondary text-white' }}">
        <i class="fas fa-times-circle me-1"></i> Ditolak
        @if(($counts['ditolak'] ?? 0) > 0)
            <span class="badge bg-dark ms-1">{{ $counts['ditolak'] }}</span>
        @endif
    </a>
    <a href="{{ route('ketua.aspirations.index', ['filter' => 'semua']) }}"
       class="btn btn-sm rounded-pill px-4 py-2 fw-bold {{ ($filter ?? '') === 'semua' ? 'btn-primary' : 'btn-outline-secondary text-white' }}">
        <i class="fas fa-list me-1"></i> Semua
    </a>
</div>

<div class="stat-card p-0 overflow-hidden border-0 shadow-lg">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>Pelapor</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Opsi</th>
                </tr>
            </thead>
            <tbody class="text-secondary">
                @forelse ($inputs as $input)
                <tr>
                    <td class="ps-4 text-white-50">{{ $loop->iteration + ($inputs->currentPage() - 1) * $inputs->perPage() }}</td>
                    <td class="fw-bold text-white">{{ $input->student->username ?? '-' }}</td>
                    <td>
                        <span class="text-info"><i class="fas fa-tag small me-1"></i> {{ $input->category->category_name }}</span>
                    </td>
                    <td><i class="fas fa-map-marker-alt text-danger small me-1"></i> {{ $input->location }}</td>
                    <td>
                        @php
                            $badge_class = match($input->submission_status) {
                                'reviewed' => 'bg-warning text-warning',
                                'diterima' => 'bg-success text-success',
                                'ditolak' => 'bg-danger text-danger',
                                default => 'bg-secondary text-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badge_class }} bg-opacity-10 border border-current px-3 py-2 text-uppercase" style="font-size: 0.7rem;">
                            {{ $input->submission_status }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('ketua.aspirations.show', $input->id_input) }}" 
                           class="btn btn-sm btn-primary px-3 rounded-pill fw-bold">
                           {{ $input->submission_status === 'reviewed' ? 'Review' : 'Detail' }} <i class="fas fa-chevron-right ms-1" style="font-size: 0.7rem;"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-secondary opacity-50">
                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        @if(($filter ?? 'reviewed') === 'reviewed')
                            Tidak ada aspirasi yang perlu direview.
                        @elseif($filter === 'diterima')
                            Belum ada aspirasi yang disetujui.
                        @elseif($filter === 'ditolak')
                            Belum ada aspirasi yang ditolak.
                        @else
                            Belum ada data aspirasi.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($inputs->hasPages())
    <div class="px-4 py-3 border-top border-secondary border-opacity-25">
        {{ $inputs->appends(['filter' => $filter])->links() }}
    </div>
    @endif
</div>
@endsection