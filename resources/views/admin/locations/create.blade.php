@extends('layout.dashboard')
@section('title', 'Tambah Lokasi')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.locations.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h4 class="fw-bold text-white mb-0" style="background: linear-gradient(120deg, #6c4ef6 0%, #4f7eff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Buat Lokasi Baru</h4>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="stat-card border-0 shadow-lg">
            <form method="POST" action="{{ route('admin.locations.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="text-label mb-2 d-block">Nama Lokasi</label>
                    <input type="text" name="location_name" value="{{ old('location_name') }}" class="form-control" placeholder="Misal: Ruang Kelas" required>
                    @error('location_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="text-label mb-2 d-block">Tipe Lokasi</label>
                    <select name="location_type" class="form-select" required>
                        <option value="" disabled {{ old('location_type') ? '' : 'selected' }}>Pilih tipe lokasi...</option>
                        <option value="kelas" {{ old('location_type') === 'kelas' ? 'selected' : '' }}>Kelas</option>
                        <option value="toilet" {{ old('location_type') === 'toilet' ? 'selected' : '' }}>Toilet</option>
                        <option value="lab" {{ old('location_type') === 'lab' ? 'selected' : '' }}>Laboratorium</option>
                        <option value="perpustakaan" {{ old('location_type') === 'perpustakaan' ? 'selected' : '' }}>Perpustakaan</option>
                        <option value="kantin" {{ old('location_type') === 'kantin' ? 'selected' : '' }}>Kantin</option>
                        <option value="lapangan" {{ old('location_type') === 'lapangan' ? 'selected' : '' }}>Lapangan</option>
                        <option value="koridor" {{ old('location_type') === 'koridor' ? 'selected' : '' }}>Koridor</option>
                        <option value="lainnya" {{ old('location_type') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('location_type')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-4 form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <label class="form-check-label text-secondary" for="is_active">
                        Aktif
                    </label>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary px-4 py-2 fw-bold flex-fill">SIMPAN LOKASI</button>
                    <a href="{{ route('admin.locations.index') }}" class="btn btn-outline-secondary px-4 py-2 flex-fill">BATAL</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
