@extends('layout.dashboard')
@section('title', 'Edit Siswa')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary text-white rounded-circle">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h4 class="fw-bold text-white mb-0">Update Data Siswa</h4>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="stat-card border-0 shadow-lg">
            <form method="POST" action="{{ route('admin.students.update', $student->id_student) }}">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="text-label mb-2 d-block text-warning">NIS (ID: #{{ $student->id_student }})</label>
                    <input type="number" name="nis" value="{{ $student->nis }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="text-label mb-2 d-block text-warning">Username</label>
                    <input type="text" name="username" value="{{ $student->username }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="text-label mb-2 d-block text-warning">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin diubah">
                    <small class="text-secondary mt-1 d-block opacity-50">Isi hanya jika ingin mengganti password lama.</small>
                </div>

                <div class="mb-4">
                    <label class="text-label mb-2 d-block text-warning">Kelas</label>
                    <input type="text" name="class" value="{{ $student->class }}" class="form-control" required>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-warning px-4 py-2 fw-bold flex-fill">UPDATE DATA</button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary px-4 py-2 flex-fill text-white">BATAL</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection