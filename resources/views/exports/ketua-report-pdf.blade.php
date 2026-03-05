<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tahunan {{ $year }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1e293b; }
        
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #3d5afe; padding-bottom: 20px; }
        .header h1 { font-size: 20px; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: 2px; }
        .header .subtitle { color: #64748b; font-size: 10px; margin-top: 5px; }
        .header .year-tag { display: inline-block; background: #3d5afe; color: #fff; padding: 4px 16px; border-radius: 4px; font-size: 12px; font-weight: 700; margin-top: 10px; }
        .header .date { color: #94a3b8; font-size: 9px; margin-top: 8px; }

        .stats-row { display: table; width: 100%; margin-bottom: 20px; }
        .stat-box { display: table-cell; text-align: center; padding: 12px 6px; border: 1px solid #e2e8f0; }
        .stat-box .number { font-size: 22px; font-weight: 900; color: #0f172a; }
        .stat-box .label { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; }

        .section-title { font-size: 13px; font-weight: 900; color: #0f172a; margin: 25px 0 10px; padding-bottom: 5px; border-bottom: 2px solid #3d5afe; display: inline-block; }

        .rate-row { display: table; width: 100%; margin-bottom: 20px; }
        .rate-box { display: table-cell; width: 33.33%; padding: 10px 8px; border: 1px solid #e2e8f0; }
        .rate-box .label { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; }
        .rate-box .value { font-size: 18px; font-weight: 900; color: #0f172a; }

        .monthly-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .monthly-table th { background: #f8fafc; color: #64748b; font-size: 8px; text-transform: uppercase; padding: 6px 4px; text-align: center; border: 1px solid #e2e8f0; }
        .monthly-table td { text-align: center; padding: 8px 4px; border: 1px solid #e2e8f0; font-weight: 700; font-size: 11px; }

        .category-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .category-table th { background: #0f172a; color: #fff; padding: 8px; font-size: 9px; text-transform: uppercase; letter-spacing: 1px; text-align: left; }
        .category-table td { padding: 8px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }
        .category-table tr:nth-child(even) { background: #f8fafc; }

        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px; }
        table.data thead th { 
            background: #0f172a; color: #fff; padding: 8px 6px; font-size: 8px; 
            text-transform: uppercase; letter-spacing: 1px; font-weight: 700; text-align: left;
        }
        table.data tbody td { 
            padding: 8px 6px; border-bottom: 1px solid #f1f5f9; font-size: 10px; 
            vertical-align: top; 
        }
        table.data tbody tr:nth-child(even) { background: #f8fafc; }

        .badge { 
            display: inline-block; padding: 2px 6px; border-radius: 3px; 
            font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .badge-selesai { background: #d1fae5; color: #065f46; }
        .badge-proses { background: #fef3c7; color: #92400e; }
        .badge-belum { background: #fee2e2; color: #991b1b; }

        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 15px; }
        .page-break { page-break-after: always; }
        .text-success { color: #065f46; }
        .text-danger { color: #991b1b; }
    </style>
</head>
<body>
    {{-- Page 1: Summary --}}
    <div class="header">
        <h1>📊 Laporan Tahunan</h1>
        <p class="subtitle">Sistem Pengaduan Sarana & Prasarana</p>
        <div class="year-tag">Tahun {{ $year }}</div>
        <div class="date">Dicetak: {{ now()->format('d F Y, H:i') }} WIB</div>
    </div>

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-box">
            <div class="number">{{ $stats['totalMasuk'] }}</div>
            <div class="label">Total Masuk</div>
        </div>
        <div class="stat-box">
            <div class="number" style="color: #065f46;">{{ $stats['diterima'] }}</div>
            <div class="label">Disetujui</div>
        </div>
        <div class="stat-box">
            <div class="number" style="color: #991b1b;">{{ $stats['ditolak'] }}</div>
            <div class="label">Ditolak</div>
        </div>
        <div class="stat-box">
            <div class="number" style="color: #92400e;">{{ $stats['menunggu'] }}</div>
            <div class="label">Menunggu</div>
        </div>
        <div class="stat-box">
            <div class="number" style="color: #1e40af;">{{ $stats['selesai'] }}</div>
            <div class="label">Tuntas</div>
        </div>
    </div>

    {{-- Rates --}}
    <div class="rate-row">
        <div class="rate-box">
            <div class="label">Tingkat Persetujuan</div>
            <div class="value">{{ $stats['approvalRate'] }}%</div>
        </div>
        <div class="rate-box">
            <div class="label">Tingkat Penyelesaian</div>
            <div class="value">{{ $stats['completionRate'] }}%</div>
        </div>
        <div class="rate-box">
            <div class="label">Kepuasan Siswa</div>
            <div class="value">{{ $stats['avgRating'] ? number_format($stats['avgRating'], 1) . '/5' : '-' }} <span style="font-size: 9px; color: #94a3b8;">({{ $stats['totalRated'] }} rating)</span></div>
        </div>
    </div>

    {{-- Monthly Trend --}}
    <div class="section-title">Tren Aspirasi Bulanan</div>
    <table class="monthly-table">
        <tr>
            @foreach($monthlyData['labels'] as $label)
            <th>{{ $label }}</th>
            @endforeach
        </tr>
        <tr>
            @foreach($monthlyData['values'] as $val)
            <td>{{ $val }}</td>
            @endforeach
        </tr>
    </table>

    {{-- Category Breakdown --}}
    <div class="section-title">Sebaran Kategori</div>
    <table class="category-table">
        <thead>
            <tr>
                <th style="width: 10%;">No</th>
                <th style="width: 55%;">Kategori</th>
                <th style="width: 15%;">Jumlah</th>
                <th style="width: 20%;">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @php $catTotal = collect($categoryData['values'])->sum(); @endphp
            @forelse(collect($categoryData['labels'])->zip($categoryData['values']) as $index => [$catName, $catCount])
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $catName }}</td>
                <td>{{ $catCount }}</td>
                <td>{{ $catTotal > 0 ? round(($catCount / $catTotal) * 100, 1) : 0 }}%</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align: center; color: #94a3b8; padding: 15px;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    @if(count($approvedAspirations) > 0 || count($rejectedAspirations) > 0)
    <div class="page-break"></div>
    @endif

    {{-- Page 2: Approved Aspirations --}}
    @if(count($approvedAspirations) > 0)
    <div class="section-title">✅ Aspirasi Disetujui ({{ count($approvedAspirations) }})</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 14%;">Siswa</th>
                <th style="width: 14%;">Kategori</th>
                <th style="width: 12%;">Lokasi</th>
                <th style="width: 30%;">Deskripsi</th>
                <th style="width: 12%;">Progress</th>
                <th style="width: 13%;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($approvedAspirations as $idx => $item)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $item->student->username ?? 'N/A' }}</td>
                <td>{{ $item->category->category_name ?? '-' }}</td>
                <td>{{ $item->location }}</td>
                <td>{{ Str::limit($item->description, 70) }}</td>
                <td>
                    @if($item->aspiration)
                        @php
                            $ps = $item->aspiration->progress_status;
                            $badgeClass = match($ps) {
                                'Belum Dimulai' => 'badge-belum',
                                'Dalam Proses' => 'badge-proses',
                                'Selesai' => 'badge-selesai',
                                default => ''
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $ps }}</span>
                    @else
                        <span style="color: #94a3b8;">-</span>
                    @endif
                </td>
                <td>{{ $item->created_at->format('d M Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Rejected Aspirations --}}
    @if(count($rejectedAspirations) > 0)
    <div class="section-title">❌ Aspirasi Ditolak ({{ count($rejectedAspirations) }})</div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 14%;">Siswa</th>
                <th style="width: 14%;">Kategori</th>
                <th style="width: 12%;">Lokasi</th>
                <th style="width: 35%;">Deskripsi</th>
                <th style="width: 20%;">Alasan Penolakan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rejectedAspirations as $idx => $item)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $item->student->username ?? 'N/A' }}</td>
                <td>{{ $item->category->category_name ?? '-' }}</td>
                <td>{{ $item->location }}</td>
                <td>{{ Str::limit($item->description, 70) }}</td>
                <td>{{ $item->aspiration->rejection_reason ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Pengaduan Sarana & Prasarana</p>
        <p>Laporan Tahun {{ $year }} — Dicetak: {{ now()->format('d F Y') }}</p>
    </div>
</body>
</html>
