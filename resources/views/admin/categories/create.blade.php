@extends('layout.dashboard')
@section('title', 'Tambah Kategori')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary text-white rounded-circle">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h4 class="fw-bold text-white mb-0">Buat Kategori Baru</h4>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="stat-card border-0 shadow-lg">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="text-label mb-2 d-block text-info">Nama Kategori</label>
                    <input type="text" name="category_name" class="form-control" placeholder="Misal: Sarana Kelas, Lapangan, dll" required>
                </div>

                <div class="mb-4">
                    <label class="text-label mb-2 d-block text-info">Deskripsi Singkat</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan cakupan kategori ini..." required></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary px-4 py-2 fw-bold flex-fill">SIMPAN KATEGORI</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-4 py-2 flex-fill text-white">BATAL</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection