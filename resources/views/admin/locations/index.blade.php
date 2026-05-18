@extends('layout.dashboard')
@section('title', 'Data Lokasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-white mb-0" style="background: linear-gradient(120deg, #6c4ef6 0%, #4f7eff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Master Lokasi</h4>
        <p class="text-secondary small mb-0">Kelola daftar lokasi fasilitas sekolah</p>
    </div>
    <a href="{{ route('admin.locations.create') }}" class="btn btn-primary btn-custom">
        <i class="fas fa-plus me-2"></i> Tambah Lokasi
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success bg-success text-white border-0 shadow-sm">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger bg-danger text-white border-0 shadow-sm">{{ session('error') }}</div>
@endif

<div class="stat-card p-0 overflow-hidden border-0 shadow-lg">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th class="ps-4" width="80">#</th>
                    <th>Nama Lokasi</th>
                    <th>Tipe</th>
                    <th>Status</th>
                    <th class="text-end pe-4" width="150">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-secondary">
                @forelse ($locations as $location)
                <tr>
                    <td class="ps-4 text-secondary">{{ $locations->firstItem() + $loop->index }}</td>
                    <td>
                        <span class="fw-bold text-white d-block">{{ $location->location_name }}</span>
                    </td>
                    <td class="small text-uppercase">{{ $location->location_type }}</td>
                    <td>
                        @if($location->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.locations.edit', $location->id_location) }}"
                               class="btn btn-sm btn-outline-warning border-0" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.locations.destroy', $location->id_location) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger border-0"
                                        onclick="return confirm('Hapus lokasi ini?')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-secondary">Belum ada data lokasi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($locations->hasPages())
    <div class="mt-3">
        {{ $locations->links() }}
    </div>
@endif
@endsection
