@extends('layout.dashboard')
@section('title', 'Detail Aspirasi')

@section('content')
<div class="mb-4 d-flex align-items-center gap-3">
    <a href="{{ route('ketua.aspirations.index') }}" class="btn btn-sm btn-outline-secondary text-white rounded-circle">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h4 class="fw-bold text-white mb-0">Review Aspirasi</h4>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="stat-card border-0 mb-4">
            <div class="text-label mb-4 text-primary">Data Laporan</div>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="text-secondary small d-block mb-1">SISWA / PELAPOR</label>
                    <p class="fw-bold text-white"><i class="fas fa-user-circle me-2 text-primary"></i> {{ $input->student->username }}</p>
                </div>
                <div class="col-md-6">
                    <label class="text-secondary small d-block mb-1">KATEGORI & LOKASI</label>
                    <p class="fw-bold text-white">
                        <span class="text-info">{{ $input->category->category_name }}</span> 
                        <span class="text-secondary mx-2">|</span> 
                        <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $input->location }}
                    </p>
                </div>
            </div>

            <div class="p-3 rounded-3 mb-4" style="background: rgba(255,255,255,0.02); border-left: 3px solid #3d5afe;">
                <label class="text-secondary small d-block mb-2 fw-bold">DESKRIPSI KEJADIAN:</label>
                <p class="text-white-50 mb-0" style="line-height: 1.6;">"{{ $input->description }}"</p>
            </div>

            @if ($input->image)
            <div class="mb-4">
                <label class="text-secondary small d-block mb-2 fw-bold">BUKTI FOTO:</label>
                <div class="position-relative overflow-hidden rounded-3 border border-secondary border-opacity-25" style="max-width: 500px;">
                    <img src="{{ asset('storage/' . $input->image) }}" class="w-100 d-block shadow-sm" alt="Bukti Foto Aspirasi">
                </div>
            </div>
            @endif

            <div class="p-3 rounded-3" style="background: rgba(61, 90, 254, 0.05); border: 1px dashed rgba(61, 90, 254, 0.3);">
                <label class="text-primary small d-block mb-1 fw-bold"><i class="fas fa-comment-dots me-1"></i> CATATAN ADMIN:</label>
                <p class="text-white mb-0 italic">-- {{ $input->admin_message ?? 'Tidak ada pesan tambahan dari admin.' }}</p>
            </div>
        </div>

        {{-- Feedback Timeline --}}
        @if ($input->aspiration && $input->aspiration->feedbacks->count())
        <div class="stat-card border-0 mb-4">
            <div class="text-label mb-4 text-primary"><i class="fas fa-stream me-1"></i> Riwayat Feedback</div>
            <div class="d-flex flex-column gap-3" style="max-height: 500px; overflow-y: auto;">
                @foreach ($input->aspiration->feedbacks->sortByDesc('feedback_at') as $fb)
                <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.02); border-left: 3px solid {{ $fb->user && $fb->user->hasRole('ketua_yayasan') ? '#ffc107' : '#3d5afe' }};">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white small fw-bold">
                            <i class="fas {{ $fb->user && $fb->user->hasRole('ketua_yayasan') ? 'fa-crown text-warning' : 'fa-user-shield text-primary' }} me-1"></i>
                            {{ $fb->user->username ?? 'System' }}
                            <span class="badge bg-secondary bg-opacity-25 text-secondary ms-2" style="font-size: 9px;">
                                {{ $fb->user && $fb->user->hasRole('ketua_yayasan') ? 'KETUA' : 'ADMIN' }}
                            </span>
                        </span>
                        <small class="text-muted">{{ $fb->feedback_at->format('d M Y') }}</small>
                    </div>
                    <p class="text-white-50 mb-0 small">{{ $fb->message }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="stat-card border-0 sticky-top" style="top: 20px;">
            @if ($input->submission_status === 'reviewed')
            {{-- Belum diputuskan - tampilkan tombol approve/reject --}}
            <div class="text-label mb-4">Ambil Keputusan</div>
            
            <div class="alert bg-dark border-secondary border-opacity-25 text-secondary small mb-4">
                <i class="fas fa-info-circle me-2"></i> Dengan menekan Approve, Anda menyetujui anggaran/tindakan untuk perbaikan ini.
            </div>

            <div class="d-grid gap-3">
                <form method="POST" action="{{ route('ketua.aspirations.approve', $input->id_input) }}" id="approveForm">
                    @csrf
                    <button type="button" class="btn btn-success w-100 py-3 fw-bold shadow-sm" onclick="confirmApprove()">
                        <i class="fas fa-check me-2"></i> APPROVE
                    </button>
                </form>

                <form method="POST" action="{{ route('ketua.aspirations.reject', $input->id_input) }}" id="rejectForm">
                    @csrf
                    <div class="mb-3">
                        <label class="text-secondary small d-block mb-2 fw-bold">ALASAN PENOLAKAN (opsional):</label>
                        <textarea name="rejection_reason" class="form-control bg-dark text-white border-secondary border-opacity-25" rows="3" placeholder="Contoh: Anggaran belum mencukupi..."></textarea>
                    </div>
                    <button type="button" class="btn btn-outline-danger w-100 py-3 fw-bold" onclick="confirmReject()">
                        <i class="fas fa-times me-2"></i> REJECT Laporan
                    </button>
                </form>
            </div>

            @elseif ($input->submission_status === 'diterima')
            {{-- Sudah disetujui --}}
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 64px; height: 64px; background: rgba(40, 167, 69, 0.1);">
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
                <h6 class="fw-bold text-success mb-1">DISETUJUI</h6>
                <p class="text-secondary small mb-0">Aspirasi ini telah Anda setujui</p>
            </div>

            @if ($input->aspiration)
            <div class="p-3 rounded-3 mb-3" style="background: rgba(255,255,255,0.02);">
                <label class="text-secondary small d-block mb-1 fw-bold">STATUS PROGRESS</label>
                <span class="badge {{ $input->aspiration->progress_status === 'Selesai' ? 'bg-success' : 'bg-info' }} bg-opacity-10 text-{{ $input->aspiration->progress_status === 'Selesai' ? 'success' : 'info' }} border border-current px-3 py-2">
                    {{ $input->aspiration->progress_status }}
                </span>
            </div>
            @endif

            @elseif ($input->submission_status === 'ditolak')
            {{-- Sudah ditolak --}}
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 64px; height: 64px; background: rgba(220, 53, 69, 0.1);">
                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                </div>
                <h6 class="fw-bold text-danger mb-1">DITOLAK</h6>
                <p class="text-secondary small mb-0">Aspirasi ini telah Anda tolak</p>
            </div>

            @if ($input->admin_message)
            <div class="p-3 rounded-3 mb-3" style="background: rgba(220, 53, 69, 0.05); border-left: 3px solid #dc3545;">
                <label class="text-secondary small d-block mb-1 fw-bold">ALASAN PENOLAKAN</label>
                <p class="text-white-50 mb-0 small">{{ $input->admin_message }}</p>
            </div>
            @endif
            @endif

            {{-- Ketua Feedback Form (for approved aspirations) --}}
            @if ($input->submission_status === 'diterima' && $input->aspiration)
            <div class="border-top border-secondary border-opacity-10 pt-4 mt-4">
                <p class="text-label small mb-3"><i class="fas fa-crown text-warning me-1"></i> Arahan Ketua</p>
                <form method="POST" action="{{ route('ketua.aspirations.feedback.store', $input->aspiration->id_aspiration) }}">
                    @csrf
                    <div class="mb-3">
                        <textarea name="message" class="form-control bg-dark text-white border-secondary border-opacity-25" rows="3" placeholder="Contoh: Prioritaskan perbaikan ini sebelum ujian..." required></textarea>
                    </div>
                    <button class="btn btn-warning w-100 fw-bold py-2 shadow-sm text-dark">
                        KIRIM ARAHAN <i class="fas fa-paper-plane ms-2"></i>
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmApprove() {
    Swal.fire({
        title: 'Approve Aspirasi?',
        text: 'Anda yakin menyetujui perbaikan ini? Tindakan ini tidak dapat dibatalkan.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Approve!',
        cancelButtonText: 'Batal',
        background: '#12141c',
        color: '#f1f1f1',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('approveForm').submit();
        }
    });
}

function confirmReject() {
    Swal.fire({
        title: 'Reject Aspirasi?',
        text: 'Anda yakin menolak aspirasi ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Reject!',
        cancelButtonText: 'Batal',
        background: '#12141c',
        color: '#f1f1f1',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('rejectForm').submit();
        }
    });
}
</script>
@endpush
@endsection