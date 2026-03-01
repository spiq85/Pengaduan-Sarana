@extends('layout.dashboard')
@section('title', 'Tambah Siswa')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-secondary text-white rounded-circle">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h4 class="fw-bold text-white mb-0">Registrasi Siswa Baru</h4>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="stat-card border-0 shadow-lg">
            <form method="POST" action="{{ route('admin.students.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="text-label mb-2 d-block text-primary">Nomor Induk Siswa (NIS)</label>
                    <input type="number" name="nis" class="form-control" placeholder="Contoh: 21221001" required>
                </div>

                <div class="mb-3">
                    <label class="text-label mb-2 d-block text-primary">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Username untuk login..." required>
                </div>

                <div class="mb-3">
                    <label class="text-label mb-2 d-block text-primary">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
                </div>

                <div class="mb-4">
                    <label class="text-label mb-2 d-block text-primary">Kelas</label>
                    <input type="text" name="class" class="form-control" placeholder="Contoh: XII PPLG 1" required>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary px-4 py-2 fw-bold flex-fill">SIMPAN DATA</button>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary px-4 py-2 flex-fill text-white">BATAL</a>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="stat-card border-start border-primary border-4" style="background: rgba(61, 90, 254, 0.05);">
            <h6 class="text-primary fw-bold"><i class="fas fa-info-circle me-2"></i> Panduan</h6>
            <ul class="text-secondary small ps-3 mt-3">
                <li>NIS harus unik dan tidak boleh sama.</li>
                <li>Username digunakan siswa untuk masuk ke sistem.</li>
                <li>Password akan di-encrypt secara otomatis oleh sistem.</li>
            </ul>
        </div>
    </div>
</div>
@endsection