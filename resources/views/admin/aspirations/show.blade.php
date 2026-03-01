@extends('layout.dashboard')
@section('title', 'Detail Aspirasi')

@section('content')
<div class="mb-5 d-flex align-items-center gap-3">
    <a href="{{ route('admin.aspirations.index') }}" class="btn btn-dark btn-sm rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold text-white mb-0">Aspiration Detail</h4>
        <span class="text-secondary small">Ticket ID: #{{ $input->id_input }}</span>
    </div>
</div>

<div class="row g-4">
    {{-- Kolom Kiri --}}
    <div class="col-lg-8">
        <div class="stat-card p-4 border-0 shadow-sm" style="background: linear-gradient(145deg, #161822, #12141c);">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <h6 class="text-label text-primary text-uppercase fw-bold" style="letter-spacing: 1px;">Information Log</h6>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2">
                    {{ $input->category->category_name ?? 'General' }}
                </span>
            </div>

            <div class="row mb-4 g-3">
                <div class="col-md-3">
                    <p class="text-secondary small mb-1">Pelapor</p>
                    <p class="fw-bold mb-0 text-white text-truncate"><i class="far fa-user me-2 text-primary"></i> {{ $input->student->username ?? 'Anonymous' }}</p>
                </div>
                <div class="col-md-3">
                    <p class="text-secondary small mb-1">Lokasi</p>
                    <p class="fw-bold mb-0 text-white text-truncate"><i class="fas fa-map-marker-alt me-2 text-danger"></i> {{ $input->location }}</p>
                </div>
                <div class="col-md-3">
                    <p class="text-secondary small mb-1">Mulai Pengerjaan</p>
                    <p class="fw-bold mb-0 text-white">
                        <i class="far fa-play-circle me-2 text-success"></i>
                        {{ $aspiration && $aspiration->start_at ? $aspiration->start_at->format('d M Y') : '-' }}
                    </p>
                </div>
                <div class="col-md-3">
                    <p class="text-secondary small mb-1">Deadline SLA</p>
                    <p class="fw-bold mb-0 text-white">
                        <i class="far fa-calendar-check me-2 text-warning"></i>
                        {{ $aspiration && $aspiration->end_at ? $aspiration->end_at->format('d M Y') : '-' }}
                    </p>
                </div>
            </div>

            <div class="p-3 rounded-3 mb-4" style="background: rgba(0,0,0,0.2); border-left: 3px solid #3d5afe;">
                <p class="text-secondary small mb-2 text-uppercase fw-bold">Deskripsi Kejadian:</p>
                <p class="mb-0 text-white-50" style="line-height: 1.6;">"{{ $input->description }}"</p>
            </div>

            @if ($input->image)
            <div>
                <p class="text-secondary small mb-2">Lampiran Bukti:</p>
                <div class="position-relative overflow-hidden rounded-3 border border-secondary border-opacity-25" style="max-width: 400px;">
                    <img src="{{ asset('storage/' . $input->image) }}" class="w-100 d-block shadow-sm">
                </div>
            </div>
            @endif
        </div>

        {{-- Kepuasan Siswa --}}
        @if ($input->rating)
        <div class="stat-card p-4 mt-4 border-0 shadow-sm" style="background: linear-gradient(145deg, #161822, #12141c);">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fas fa-star text-warning"></i>
                <h6 class="text-label text-uppercase fw-bold mb-0" style="letter-spacing: 1px;">Kepuasan Siswa</h6>
            </div>

            <div class="row g-4 align-items-center">
                {{-- Score --}}
                <div class="col-md-4">
                    <div class="text-center p-4 rounded-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                        <div class="mb-2">
                            <span class="display-3 fw-bold text-white">{{ $input->rating }}</span>
                            <span class="text-secondary fs-5">/5</span>
                        </div>
                        <div class="d-flex justify-content-center gap-1 mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $input->rating ? 'fas' : 'far' }} fa-star {{ $i <= $input->rating ? 'text-warning' : 'text-secondary' }}" style="font-size: 1.1rem;"></i>
                            @endfor
                        </div>
                        <span class="badge {{ $input->rating >= 4 ? 'bg-success' : ($input->rating >= 3 ? 'bg-warning text-dark' : 'bg-danger') }} bg-opacity-25 {{ $input->rating >= 4 ? 'text-success' : ($input->rating >= 3 ? 'text-warning' : 'text-danger') }} px-3 py-1">
                            {{ $input->rating >= 4 ? 'Puas' : ($input->rating >= 3 ? 'Cukup' : 'Kurang Puas') }}
                        </span>
                    </div>
                </div>

                {{-- Feedback Siswa --}}
                <div class="col-md-8">
                    <div class="p-3 rounded-3 h-100" style="background: rgba(0,0,0,0.2); border-left: 3px solid #ffc107;">
                        <p class="text-secondary small mb-2 text-uppercase fw-bold">
                            <i class="fas fa-comment-dots me-1"></i> Feedback dari Siswa
                        </p>
                        <p class="mb-0 text-white-50 fst-italic" style="line-height: 1.7;">
                            "{{ $input->feedback ?: 'Tidak ada komentar tambahan.' }}"
                        </p>
                        <div class="mt-3 pt-2 border-top border-secondary border-opacity-10">
                            <small class="text-secondary">
                                <i class="far fa-user me-1"></i> {{ $input->student->username ?? 'Siswa' }}
                                <span class="mx-2">•</span>
                                <i class="far fa-clock me-1"></i> {{ $input->updated_at->format('d M Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @elseif ($input->submission_status === 'diterima' && $aspiration && $aspiration->progress_status === 'Selesai')
        <div class="stat-card p-4 mt-4 border-0 shadow-sm" style="background: linear-gradient(145deg, #161822, #12141c);">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fas fa-star text-secondary"></i>
                <h6 class="text-label text-uppercase fw-bold mb-0" style="letter-spacing: 1px;">Kepuasan Siswa</h6>
            </div>
            <div class="text-center py-4 rounded-3" style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.08);">
                <i class="far fa-clock text-secondary mb-2" style="font-size: 2rem;"></i>
                <p class="text-secondary small mb-0 mt-2">Menunggu siswa memberikan rating...</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Kolom Kanan --}}
    <div class="col-lg-4">
        <div class="stat-card p-4 mb-4 border-0" style="background: rgba(255,255,255,0.01);">
            <h6 class="text-label mb-3 text-uppercase fw-bold small" style="letter-spacing: 1px;">System Action</h6>

            <div class="text-center py-3 rounded-3 mb-4" style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1);">
                <p class="text-secondary small mb-1">Status Saat Ini</p>
                <h4 class="fw-bold text-uppercase mb-0 {{ $input->submission_status == 'ditolak' ? 'text-danger' : ($input->submission_status == 'diterima' ? 'text-success' : 'text-warning') }}">
                    {{ $input->submission_status }}
                </h4>
            </div>

            @if ($input->submission_status === 'menunggu')
            <form action="{{ route('admin.aspirations.send', $input->id_input) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <textarea name="admin_message" class="form-control bg-dark text-white border-secondary border-opacity-25" rows="3" placeholder="Tambahkan catatan untuk Yayasan..." required></textarea>
                </div>
                <button class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                    FORWARD TO KETUA <i class="fas fa-chevron-right ms-2"></i>
                </button>
            </form>
            @endif

            @if ($input->submission_status === 'diterima' && $aspiration)
            <div class="border-top border-secondary border-opacity-10 pt-4 mt-2">
                <div class="p-3 rounded-3 mb-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="text-secondary small mb-0"><i class="fas fa-stopwatch me-1"></i> Target SLA</p>
                        <span class="badge {{ $aspiration->priority_level == 'Emergency' ? 'bg-danger' : ($aspiration->priority_level == 'Urgent' ? 'bg-warning' : 'bg-info') }}">
                            {{ $aspiration->priority_level }}
                        </span>
                    </div>
                    <h5 class="fw-bold text-white mb-1">
                        {{ $aspiration->end_at ? $aspiration->end_at->format('d M Y') : 'Not Set' }}
                    </h5>
                    @if($aspiration->end_at)
                    <small class="text-white-50">
                        Batas waktu: <span class="text-white-50">{{ (int) now()->diffInDays($aspiration->end_at) }} hari lagi</span>
                    </small>
                    @endif
                </div>

                <p class="text-label small mb-3">Update Progress Perbaikan</p>
                <form method="POST" action="{{ route('admin.aspirations.progress.update', $aspiration->id_aspiration) }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <select name="progress_status" class="form-select bg-dark text-white border-secondary border-opacity-25">
                            <option value="Belum Dimulai" {{ $aspiration->progress_status === 'Belum Dimulai' ? 'selected' : '' }}>🔴 Belum Dimulai</option>
                            <option value="Dalam Proses" {{ $aspiration->progress_status === 'Dalam Proses' ? 'selected' : '' }}>🟡 Dalam Proses</option>
                            <option value="Selesai" {{ $aspiration->progress_status === 'Selesai' ? 'selected' : '' }}>🟢 Selesai</option>
                        </select>
                    </div>
                    <button class="btn btn-success w-100 fw-bold py-2 shadow-sm">
                        SAVE PROGRESS <i class="fas fa-save ms-2"></i>
                    </button>
                </form>

                {{-- Feedback Form --}}
                <div class="border-top border-secondary border-opacity-10 pt-4 mt-4">
                    <p class="text-label small mb-3"><i class="fas fa-comment-dots me-1"></i> Kirim Feedback / Update</p>
                    <form method="POST" action="{{ route('admin.aspirations.feedback.store', $aspiration->id_aspiration) }}">
                        @csrf
                        <div class="mb-3">
                            <textarea name="message" class="form-control bg-dark text-white border-secondary border-opacity-25" rows="3" placeholder="Contoh: Kursi sudah dipesan, estimasi 2 minggu..." required></textarea>
                        </div>
                        <button class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                            KIRIM FEEDBACK <i class="fas fa-paper-plane ms-2"></i>
                        </button>
                    </form>
                </div>

                {{-- Feedback Timeline --}}
                @if ($aspiration->feedbacks->count())
                <div class="border-top border-secondary border-opacity-10 pt-4 mt-4">
                    <p class="text-label small mb-3"><i class="fas fa-stream me-1"></i> Riwayat Feedback</p>
                    <div class="d-flex flex-column gap-3" style="max-height: 400px; overflow-y: auto;">
                        @foreach ($aspiration->feedbacks->sortByDesc('feedback_at') as $fb)
                        <div class="p-3 rounded-3" style="background: rgba(61, 90, 254, 0.05); border-left: 3px solid {{ $fb->user && $fb->user->hasRole('ketua_yayasan') ? '#ffc107' : '#3d5afe' }};">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-white small fw-bold">
                                    <i class="fas {{ $fb->user && $fb->user->hasRole('ketua_yayasan') ? 'fa-crown text-warning' : 'fa-user-shield text-primary' }} me-1"></i>
                                    {{ $fb->user->username ?? 'System' }}
                                    <span class="badge bg-secondary bg-opacity-25 text-secondary ms-2" style="font-size: 9px;">
                                        {{ $fb->user && $fb->user->hasRole('ketua_yayasan') ? 'KETUA' : 'ADMIN' }}
                                    </span>
                                </span>
                                <small class="text-white small mb-0">{{ $fb->feedback_at->format('d M Y') }}</small>
                            </div>
                            <p class="text-white-50 mb-0 small">{{ $fb->message }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Log Aktivitas --}}
        <div class="stat-card p-4 border-0" style="background: rgba(255,255,255,0.01);">
            <h6 class="text-label mb-3 text-uppercase fw-bold small">Last Activity</h6>
            <div class="d-flex gap-3 mb-2">
                <div class="text-primary small"><i class="fas fa-circle"></i></div>
                <div>
                    @if($input->submission_status === 'diterima' && $aspiration)
                        <p class="text-white small mb-0">Disetujui Ketua</p>
                        <small class="text-white small mb-0">{{ $aspiration->validated_at ? $aspiration->validated_at->format('d M Y, H:i') : '-' }}</small>
                    @elseif($input->submission_status === 'reviewed')
                        <p class="text-white small mb-0">Diteruskan ke Ketua</p>
                        <small class="text-white small mb-0">{{ $input->updated_at->format('d M Y, H:i') }}</small>
                    @else
                        <p class="text-white small mb-0">Aspirasi Masuk</p>
                        <small class="text-white small mb-0">{{ $input->created_at->format('d M Y, H:i') }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection