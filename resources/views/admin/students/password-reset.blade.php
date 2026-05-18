@extends('layout.dashboard')
@section('title', 'Permintaan Reset Password')

@section('content')
<h3 class="fw-bold text-white mb-4" style="background: linear-gradient(120deg, #6c4ef6 0%, #4f7eff 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Permintaan Reset Password</h3>

<div class="stat-card p-0 overflow-hidden">
        <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle" style="border-color: #dbe5ff;">
                        <thead style="background: #f7faff;">
                                <tr class="text-label">
                                        <th class="ps-4">Siswa</th>
                                        <th>NIS</th>
                                        <th>Kelas</th>
                                        <th>Pesan</th>
                                        <th>Tanggal</th>
                                        <th class="text-end pe-4">Aksi</th>
                                </tr>
                        </thead>
                        <tbody class="text-secondary">
                                @forelse($request as $req)
                                <tr>
                                        <td class="ps-4 text-white fw-medium">{{ $req->student->username }}</td>
                                        <td>{{ $req->student->nis }}</td>
                                        <td>{{ $req->student->class }}</td>
                                        <td>{{ $req->message ?? '-' }}</td>
                                        <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-end pe-4">
                                                <form action="{{ route('admin.password-resets.reset', $req->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm rounded-3"
                                                                onclick="return confirm('Reset password {{ $req->student->username }} ke NIS ({{ $req->student->nis }})?')">
                                                                <i class="fas fa-redo me-1"></i> Reset
                                                        </button>
                                                </form>
                                        </td>
                                </tr>
                                @empty
                                <tr>
                                        <td colspan="6" class="text-center py-5 opacity-50 text-secondary">
                                                <i class="fas fa-check-circle d-block mb-2 fs-2"></i>
                                                Tidak ada permintaan reset password
                                        </td>
                                </tr>
                                @endforelse
                        </tbody>
                </table>
        </div>
</div>

<div class="mt-3">{{ $request->links() }}</div>
@endsection