<div class="asp-card tone-{{ $section['tone'] }}">
    <div class="asp-card-header">
        <div>
            <div class="asp-head-main">
                <span class="asp-icon {{ $section['tone'] }}"><i class="fas {{ $section['icon'] }}"></i></span>
                <p class="asp-card-title {{ $section['textClass'] }}">{{ $section['title'] }}</p>
            </div>
            <span class="asp-card-count mt-2"><i class="fas fa-layer-group"></i> {{ $section['count'] }} aspirasi</span>
            @if(($section['lockedCount'] ?? 0) > 0)
                <div class="lock-note"><i class="fas fa-lock me-1"></i>{{ $section['lockedCount'] }} aspirasi terkunci sampai antrean sebelumnya selesai.</div>
            @endif
        </div>
        <button
            class="asp-toggle-btn"
            type="button"
            data-target="asp-section-{{ $section['id'] }}"
            onclick="toggleAspSection(this)"
        >
            <i class="fas fa-bars me-1"></i> Tutup
        </button>
    </div>

    <div id="asp-section-{{ $section['id'] }}" class="asp-card-body table-responsive">
        <table class="table table-modern table-hover mb-0 align-middle">
            <thead>
                <tr class="text-label">
                    <th class="ps-4">ID</th>
                    <th>Siswa</th>
                    <th>Judul & Info</th>
                    <th>Mode</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Tanggal</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-secondary" style="font-size: 0.9rem;">
                @forelse($section['items'] as $item)
                <tr>
                    <td class="ps-4">#{{ $item->id_input }}</td>
                    <td class="fw-semibold">{{ $item->student->username ?? '-' }}</td>
                    <td>
                        <div class="cell-title">{{ $item->title ?? '-' }}</div>
                        <div class="cell-meta">
                            <span><i class="fas fa-layer-group me-1"></i>{{ $item->category->category_name ?? '-' }}</span>
                            <span><i class="fas fa-map-marker-alt me-1"></i>{{ $item->location ?? '-' }}</span>
                            <span class="vote-badge d-inline-flex align-items-center">
                                <i class="fas fa-thumbs-up me-1 {{ ($item->aspiration->votes_count ?? 0) > 0 ? 'text-primary' : 'opacity-25' }}"></i>
                                {{ $item->aspiration->votes_count ?? 0 }}
                            </span>
                        </div>
                        @if(($item->submission_mode ?? 'template') === 'custom' && $item->submission_status === 'menunggu')
                                @php
                                    $customRank = $customPriorityRanks[$item->id_input] ?? null;
                                    $rankClass = match(true) {
                                        $customRank === 1 => 'is-top',
                                        $customRank === 2 => 'is-second',
                                        default => 'is-other',
                                    };
                                    $isLocked = (bool) ($customLockedMap[$item->id_input] ?? false);
                                    $isKeptActive = (bool) ($item->is_kept && filled($item->kept_until) && $item->kept_until->isFuture());
                                @endphp
                                <div class="cell-rank">
                                    <span class="custom-rank {{ $rankClass }}">
                                        <span>ANTREAN #{{ $customRank ?? '-' }}</span>
                                        <small class="fw-bold opacity-75">
                                            {{ $isKeptActive ? 'Sedang di-keep' : ($isLocked ? 'Locked sampai antrean sebelumnya selesai' : 'Siap diproses') }}
                                        </small>
                                    </span>
                                </div>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ ($item->submission_mode ?? 'template') === 'custom' ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }}">
                            {{ strtoupper($item->submission_mode ?? 'template') }}
                        </span>
                    </td>
                    <td>
                        @php
                            $status = $item->submission_status ?? 'menunggu';
                            $s_color = match($status) {
                                'menunggu' => 'bg-secondary',
                                'diterima' => 'bg-success',
                                'ditolak' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            
                            $isLocked = false;
                            if (($item->submission_mode ?? 'template') === 'custom') {
                                $isLocked = (bool) ($customLockedMap[$item->id_input] ?? false);
                            } else {
                                $isLocked = (bool) ($templateLockedMap[$item->id_input] ?? false);
                            }
                        @endphp
                        <span class="badge {{ $s_color }} rounded-pill" style="font-size: 0.7rem;">
                            {{ strtoupper($status) }}
                        </span>
                            @if(($item->submission_mode ?? 'template') === 'custom' && (bool) ($item->is_kept && filled($item->kept_until) && $item->kept_until->isFuture()))
                                <div class="lock-note mt-2 text-info"><i class="fas fa-pause-circle me-1"></i>Sedang di-keep</div>
                            @elseif($isLocked && $item->submission_status === 'menunggu')
                                <div class="lock-note mt-2"><i class="fas fa-lock me-1"></i>Item terkunci</div>
                            @endif
                    </td>
                    <td>
                        @if ($item->submission_status === 'diterima' && $item->aspiration)
                            @php
                                $p_status = $item->aspiration->progress_status;
                                $p_color = match($p_status) {
                                    'Belum Dimulai' => 'text-secondary',
                                    'Dalam Proses' => 'text-info',
                                    'Observasi' => 'text-warning fw-bold',
                                    'Selesai' => 'text-success fw-bold',
                                    default => 'text-secondary'
                                };
                            @endphp
                            <span class="{{ $p_color }}" style="font-size: 0.85rem;">
                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> {{ $p_status }}
                            </span>
                        @else
                            <span class="opacity-25">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span>{{ $item->created_at->format('d/m/y') }}</span>
                            @if(($item->submission_mode ?? 'template') === 'custom' && !empty($item->input_at))
                                <small class="text-danger fw-bold" style="font-size: 0.7rem;">Masuk {{ \Carbon\Carbon::parse($item->input_at)->format('H:i') }}</small>
                            @endif
                        </div>
                    </td>
                    <td class="text-end pe-4">
                            @php
                                $isLocked = ($item->submission_mode ?? 'template') === 'custom'
                                    ? (bool) ($customLockedMap[$item->id_input] ?? false)
                                    : (bool) ($templateLockedMap[$item->id_input] ?? false);
                                $isKeptActive = ($item->submission_mode ?? 'template') === 'custom' && (bool) ($item->is_kept && filled($item->kept_until) && $item->kept_until->isFuture());
                                
                                // Approved items are never locked for detail view
                                $canOpen = ($item->submission_status !== 'menunggu') || !$isLocked || $isKeptActive;
                            @endphp
                            <a href="{{ route('admin.aspirations.show', $item->id_input) }}" class="btn btn-outline-primary btn-sm rounded-3 px-3 shadow-sm hover-up {{ !$canOpen ? 'disabled pe-none opacity-50' : '' }}">
                                {{ !$canOpen ? 'Locked' : 'Detail' }}
                            </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-state">Tidak ada data di section ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
