@extends('layout.dashboard')
@section('title', 'Edit Kategori')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary text-white rounded-circle">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h4 class="fw-bold text-white mb-0">Update Kategori</h4>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="stat-card border-0 shadow-lg">
            <form method="POST" action="{{ route('admin.categories.update', $category->id_category) }}">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="text-label mb-2 d-block text-warning">Nama Kategori</label>
                    <input type="text" name="category_name" value="{{ $category->category_name }}" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="text-label mb-2 d-block text-warning">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="3" required>{{ $category->description }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-warning px-4 py-2 fw-bold flex-fill text-dark">UPDATE DATA</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-4 py-2 flex-fill text-white">BATAL</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection