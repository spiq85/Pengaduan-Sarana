@extends('layout.dashboard')
@section('title', 'Detail Aspirasi')

@section('content')
<div class="mb-5 d-flex align-items-center gap-3">
    <a href="{{ route('admin.aspirations.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
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
        <div class="stat-card p-4 border-0 shadow-sm" style="background: linear-gradient(155deg, #ffffff 0%, #f5f9ff 100%);">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <h6 class="text-label text-primary text-uppercase fw-bold" style="letter-spacing: 1px;">Information Log</h6>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2">
                    {{ $input->category->category_name ?? 'General' }}
                </span>
            </div>

            <div class="row mb-4 g-3">
                <div class="col-12">
                    <p class="text-secondary small mb-1">Judul Aspirasi</p>
                    <p class="fw-bold mb-0 text-white">{{ $input->title ?? '-' }}</p>
                </div>
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

            <div class="p-3 rounded-3 mb-4" style="background: #f4f8ff; border-left: 3px solid #5b8dff;">
                <p class="text-secondary small mb-2 text-uppercase fw-bold">Deskripsi Kejadian:</p>
                <p class="mb-0 text-secondary" style="line-height: 1.6;">"{{ $input->description }}"</p>
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
        <div class="stat-card p-4 mt-4 border-0 shadow-sm" style="background: linear-gradient(155deg, #ffffff 0%, #f8f4ff 100%);">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fas fa-star text-warning"></i>
                <h6 class="text-label text-uppercase fw-bold mb-0" style="letter-spacing: 1px;">Kepuasan Siswa</h6>
            </div>

            <div class="row g-4 align-items-center">
                {{-- Score --}}
                <div class="col-md-4">
                    <div class="text-center p-4 rounded-3" style="background: #f6f9ff; border: 1px solid #e1e9ff;">
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
                    <div class="p-3 rounded-3 h-100" style="background: #fff8e9; border-left: 3px solid #ffc107;">
                        <p class="text-secondary small mb-2 text-uppercase fw-bold">
                            <i class="fas fa-comment-dots me-1"></i> Feedback dari Siswa
                        </p>
                        <p class="mb-0 text-secondary fst-italic" style="line-height: 1.7;">
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
        <div class="stat-card p-4 mt-4 border-0 shadow-sm" style="background: linear-gradient(155deg, #ffffff 0%, #f8f4ff 100%);">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fas fa-star text-secondary"></i>
                <h6 class="text-label text-uppercase fw-bold mb-0" style="letter-spacing: 1px;">Kepuasan Siswa</h6>
            </div>
            <div class="text-center py-4 rounded-3" style="background: #f6f9ff; border: 1px dashed #dbe5ff;">
                <i class="far fa-clock text-secondary mb-2" style="font-size: 2rem;"></i>
                <p class="text-secondary small mb-0 mt-2">Menunggu siswa memberikan rating...</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Kolom Kanan --}}
    <div class="col-lg-4">
        <div class="stat-card p-4 mb-4 border-0" style="background: linear-gradient(155deg, #ffffff 0%, #f4f8ff 100%);">
            <h6 class="text-label mb-3 text-uppercase fw-bold small" style="letter-spacing: 1px;">System Action</h6>

            <div class="text-center py-3 rounded-3 mb-4" style="background: #f7faff; border: 1px dashed #dce5ff;">
                <p class="text-secondary small mb-1">Status Saat Ini</p>
                <h4 class="fw-bold text-uppercase mb-0 {{ $input->submission_status == 'ditolak' ? 'text-danger' : ($input->submission_status == 'diterima' ? 'text-success' : 'text-warning') }}">
                    {{ $input->submission_status }}
                </h4>
                @if($aspiration && str_contains($aspiration->progress_status, 'Menunggu'))
                    <span class="badge bg-warning text-dark mt-2">MENUNGGU KONFIRMASI SISWA</span>
                @endif
            </div>

            @if ($input->submission_status === 'menunggu')
            @if(($input->submission_mode ?? 'template') === 'custom')
                @if($input->is_kept && $input->kept_until && $input->kept_until->isFuture())
                    <div class="p-3 rounded-3 mb-3" style="background: rgba(13, 202, 240, 0.08); border-left: 3px solid #0dcaf0;">
                        <p class="text-info small mb-1"><i class="fas fa-clock me-1"></i> Sedang di-keep</p>
                        <p class="text-secondary small mb-0">Sampai {{ $input->kept_until->format('d M Y H:i') }}</p>
                    </div>

                    <form action="{{ route('admin.aspirations.keep.release', $input->id_input) }}" method="POST" class="mb-3">
                        @csrf
                        <button class="btn btn-outline-warning w-100 fw-bold py-2 shadow-sm">
                            LEPAS KEEP <i class="fas fa-rotate-left ms-2"></i>
                        </button>
                        <small class="text-secondary d-block mt-2">Aspirasi akan kembali ke antrean custom dan membuka item paling lama berikutnya.</small>
                    </form>
                @else
                    <form action="{{ route('admin.aspirations.keep', $input->id_input) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="mb-2">
                            <textarea name="keep_note" class="form-control" rows="2" placeholder="Alasan keep (opsional)"></textarea>
                        </div>
                        <button class="btn btn-outline-warning w-100 fw-bold py-2 shadow-sm">
                            KEEP 3 HARI <i class="fas fa-clock ms-2"></i>
                        </button>
                        <small class="text-secondary d-block mt-2">Aspirasi custom yang di-keep akan punya batas 3 hari.</small>
                    </form>
                @endif
            @endif

            <form action="{{ route('admin.aspirations.approve', $input->id_input) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <textarea name="admin_message" class="form-control" rows="3" placeholder="Catatan internal admin (opsional)"></textarea>
                </div>
                <button class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                    SETUJUI ASPIRASI <i class="fas fa-check ms-2"></i>
                </button>
            </form>

            <form action="{{ route('admin.aspirations.reject', $input->id_input) }}" method="POST" class="mt-3">
                @csrf
                <div class="mb-3">
                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Alasan penolakan (wajib)" required></textarea>
                </div>
                <button class="btn btn-outline-danger w-100 fw-bold py-2 shadow-sm">
                    TOLAK ASPIRASI <i class="fas fa-times ms-2"></i>
                </button>
            </form>
            @endif

            @if ($input->submission_status === 'diterima' && $aspiration)
            <div class="border-top border-secondary border-opacity-10 pt-4 mt-2">
                @if($aspiration->ketua_instruction)
                <div class="p-3 rounded-3 mb-4" style="background: rgba(255, 193, 7, 0.08); border-left: 3px solid #ffc107;">
                    <p class="text-warning small mb-1"><i class="fas fa-note-sticky me-1"></i> Catatan Persetujuan</p>
                    <p class="text-secondary small mb-0">{{ $aspiration->ketua_instruction }}</p>
                </div>
                @endif

                <div class="p-3 rounded-3 mb-4" style="background: #f4f8ff; border: 1px solid #e2eaff;">
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
                    <small class="text-secondary">
                        Batas waktu: <span class="text-secondary">{{ (int) now()->diffInDays($aspiration->end_at) }} hari lagi</span>
                    </small>
                    @endif
                </div>

                @if($aspiration->progress_evidence_image)
                <div class="mb-4">
                    <p class="text-secondary small mb-2"><i class="fas fa-image me-1"></i> Bukti Update Terakhir</p>
                    <div class="rounded-3 overflow-hidden border border-secondary border-opacity-25" style="max-width: 100%;">
                        <img src="{{ asset('storage/' . $aspiration->progress_evidence_image) }}" class="w-100 d-block" alt="Bukti update progress">
                    </div>
                </div>
                @endif

                <p class="text-label small mb-3">Update Progress Perbaikan</p>
                
                @if(in_array($aspiration->progress_status, ['Belum Dimulai', 'Dalam Proses']))
                <form method="POST" action="{{ route('admin.aspirations.progress.update', $aspiration->id_aspiration) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <select name="progress_status" class="form-select">
                            <option value="Belum Dimulai" {{ $aspiration->progress_status === 'Belum Dimulai' ? 'selected' : '' }}>🔴 Belum Dimulai</option>
                            <option value="Dalam Proses" {{ $aspiration->progress_status === 'Dalam Proses' ? 'selected' : '' }}>🟡 Dalam Proses</option>
                            <option value="Selesai" {{ $aspiration->progress_status === 'Selesai' ? 'selected' : '' }}>🟢 Selesai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="text-secondary small d-block mb-2">Foto Bukti Update <span class="text-danger">*</span></label>
                        <input type="file" name="evidence_image" accept="image/*" class="form-control" required>
                        <small class="text-secondary">Wajib upload foto bukti setiap update progress.</small>
                    </div>
                    <button class="btn btn-success w-100 fw-bold py-2 shadow-sm mb-3 text-uppercase">
                        SAVE PROGRESS <i class="fas fa-save ms-2"></i>
                    </button>
                </form>
                @else
                <div class="bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 p-4 text-center">
                    <div class="w-12 h-12 rounded-circle bg-success bg-opacity-20 flex items-center justify-center mx-auto mb-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-circle text-success fs-4"></i>
                    </div>
                    <p class="text-success fw-black uppercase tracking-widest small mb-1">{{ $aspiration->progress_status }}</p>
                    <p class="text-secondary small mb-0">
                        @if($aspiration->progress_status === 'Selesai')
                            Pengerjaan telah selesai.
                        @else
                            Kontrol penyelesaian kini berada di tangan siswa.
                        @endif
                    </p>
                </div>
                @endif
                <small class="text-secondary d-block mt-2">Status selesai akan menghentikan waktu SLA pengerjaan.</small>
                <small class="text-secondary d-block mt-2">Status selesai hanya bisa dikonfirmasi oleh siswa.</small>

                {{-- Feedback Form --}}
                <div class="border-top border-secondary border-opacity-10 pt-4 mt-4">
                    <p class="text-label small mb-3"><i class="fas fa-comment-dots me-1"></i> Kirim Feedback / Update</p>
                    <form method="POST" action="{{ route('admin.aspirations.feedback.store', $aspiration->id_aspiration) }}">
                        @csrf
                        <div class="mb-3">
                            <textarea name="message" class="form-control" rows="3" placeholder="Contoh: Kursi sudah dipesan, estimasi 2 minggu..." required></textarea>
                        </div>
                        <button class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                            KIRIM FEEDBACK <i class="fas fa-paper-plane ms-2"></i>
                        </button>
                    </form>
                </div>

                {{-- Feedback Timeline --}}
                {{-- Chat dari Siswa --}}
                @if ($aspiration->comments->count())
                <div class="border-top border-secondary border-opacity-10 pt-4 mt-4">
                    <p class="text-label small mb-3"><i class="fas fa-comments me-1"></i> Pesan Chat dari Siswa</p>
                    <div class="d-flex flex-column gap-3" style="max-height: 400px; overflow-y: auto;">
                        @foreach ($aspiration->comments->sortByDesc('created_at') as $cm)
                        <div class="p-3 rounded-3 border-start border-3 border-secondary" style="background: #f8f9fa;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-dark small fw-bold">
                                    <i class="fas fa-user text-secondary me-1"></i>
                                    {{ $cm->student->username ?? 'Siswa' }}
                                </span>
                                <small class="text-secondary small mb-0">{{ $cm->created_at->format('d M Y, H:i') }}</small>
                            </div>
                            <p class="text-secondary mb-0 small @if(str_contains($cm->body, 'SISWA MENOLAK')) fw-bold text-danger @endif">
                                {{ $cm->body }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if ($aspiration->feedbacks->count())
                <div class="border-top border-secondary border-opacity-10 pt-4 mt-4">
                    <p class="text-label small mb-3"><i class="fas fa-stream me-1"></i> Riwayat Feedback</p>
                    <div class="d-flex flex-column gap-3" style="max-height: 400px; overflow-y: auto;">
                        @foreach ($aspiration->feedbacks->sortByDesc('feedback_at') as $fb)
                        <div class="p-3 rounded-3 border-start border-3 border-primary" style="background: #f3f7ff;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-dark small fw-bold">
                                    <i class="fas fa-user-shield text-primary me-1"></i>
                                    {{ $fb->user->username ?? 'System' }}
                                    <span class="badge bg-secondary bg-opacity-25 text-secondary ms-2" style="font-size: 9px;">
                                        ADMIN
                                    </span>
                                </span>
                                <small class="text-secondary small mb-0">{{ $fb->feedback_at->format('d M Y') }}</small>
                            </div>
                            <p class="text-secondary mb-0 small">{{ $fb->message }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Log Aktivitas --}}
        <div class="stat-card p-4 border-0" style="background: linear-gradient(155deg, #ffffff 0%, #f7f4ff 100%);">
            <h6 class="text-label mb-3 text-uppercase fw-bold small">Last Activity</h6>
            <div class="d-flex gap-3 mb-2">
                <div class="text-primary small"><i class="fas fa-circle"></i></div>
                <div>
                    @if($input->submission_status === 'diterima' && $aspiration)
                        <p class="text-white small mb-0">Disetujui Admin</p>
                        <small class="text-white small mb-0">{{ $aspiration->validated_at ? $aspiration->validated_at->format('d M Y, H:i') : '-' }}</small>
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