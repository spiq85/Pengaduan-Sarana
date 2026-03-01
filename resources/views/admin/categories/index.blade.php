@extends('layout.dashboard')
@section('title', 'Data Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-white mb-0">Kategori Aspirasi</h4>
        <p class="text-secondary small mb-0">Klasifikasi pengaduan sarana prasarana</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-custom">
        <i class="fas fa-plus me-2"></i> Tambah Kategori
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success bg-success text-white border-0 shadow-sm">{{ session('success') }}</div>
@endif

<div class="stat-card p-0 overflow-hidden border-0 shadow-lg">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th class="ps-4" width="80">#</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-end pe-4" width="150">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-secondary">
                @foreach ($categories as $category)
                <tr>
                    <td class="ps-4 text-white-50">{{ $loop->iteration }}</td>
                    <td>
                        <span class="fw-bold text-white d-block">{{ $category->category_name }}</span>
                    </td>
                    <td class="small">{{ $category->description }}</td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.categories.edit', $category->id_category) }}" 
                               class="btn btn-sm btn-outline-warning border-0" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category->id_category) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger border-0" 
                                        onclick="return confirm('Hapus kategori ini?')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection