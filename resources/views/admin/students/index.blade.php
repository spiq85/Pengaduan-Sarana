@extends('layout.dashboard')
@section('title', 'Data Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-white mb-0">Manajemen Akun Siswa</h4>
        <p class="text-secondary small mb-0">Total terdaftar: {{ $students->total() }} siswa</p>
    </div>
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-custom">
        <i class="fas fa-plus me-2"></i> Tambah Siswa
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
                    <th class="ps-4">#</th>
                    <th>NIS</th>
                    <th>Username</th>
                    <th>Kelas</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-secondary">
                @foreach ($students as $student)
                <tr>
                    <td class="ps-4 text-white-50">{{ $loop->iteration }}</td>
                    <td class="fw-bold text-white">{{ $student->nis }}</td>
                    <td>{{ $student->username }}</td>
                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">{{ $student->class }}</span></td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.students.edit', $student->id_student) }}"
                                class="btn btn-sm btn-outline-warning border-0" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.students.destroy', $student->id_student) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger border-0"
                                    onclick="return confirm('Hapus akun siswa ini?')" title="Hapus">
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
<div class="mt-4 d-flex justify-content-center custom-pagination bg-dark-secondary p-3 rounded-3">
    {{ $students->links() }}
</div>
@endsection