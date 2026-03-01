<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aspirasi Siswa</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #1e293b; }
        
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #3d5afe; padding-bottom: 20px; }
        .header h1 { font-size: 20px; font-weight: 900; color: #0f172a; text-transform: uppercase; letter-spacing: 2px; }
        .header p { color: #64748b; font-size: 10px; margin-top: 5px; }
        .header .date { color: #3d5afe; font-weight: 700; font-size: 10px; margin-top: 8px; }

        .stats-row { display: table; width: 100%; margin-bottom: 25px; }
        .stat-box { display: table-cell; width: 25%; text-align: center; padding: 12px 8px; border: 1px solid #e2e8f0; }
        .stat-box .number { font-size: 22px; font-weight: 900; color: #0f172a; }
        .stat-box .label { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; font-weight: 700; }

        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data thead th { 
            background: #0f172a; color: #fff; padding: 10px 8px; font-size: 9px; 
            text-transform: uppercase; letter-spacing: 1px; font-weight: 700; text-align: left;
        }
        table.data tbody td { 
            padding: 10px 8px; border-bottom: 1px solid #f1f5f9; font-size: 10px; 
            vertical-align: top; 
        }
        table.data tbody tr:nth-child(even) { background: #f8fafc; }

        .badge { 
            display: inline-block; padding: 3px 8px; border-radius: 4px; 
            font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .badge-menunggu { background: #fef3c7; color: #92400e; }
        .badge-reviewed { background: #dbeafe; color: #1e40af; }
        .badge-diterima { background: #d1fae5; color: #065f46; }
        .badge-ditolak { background: #fee2e2; color: #991b1b; }
        
        .badge-belum { background: #fee2e2; color: #991b1b; }
        .badge-proses { background: #fef3c7; color: #92400e; }
        .badge-selesai { background: #d1fae5; color: #065f46; }

        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 15px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Laporan Aspirasi Siswa</h1>
        <p>Sistem Pengaduan Sarana & Prasarana</p>
        <div class="date">Dicetak: {{ now()->format('d F Y, H:i') }} WIB</div>
    </div>

    <div class="stats-row">
        <div class="stat-box">
            <div class="number">{{ $stats['total'] }}</div>
            <div class="label">Total Aspirasi</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $stats['menunggu'] }}</div>
            <div class="label">Menunggu</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $stats['diterima'] }}</div>
            <div class="label">Diterima</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $stats['ditolak'] }}</div>
            <div class="label">Ditolak</div>
        </div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Siswa</th>
                <th style="width: 12%;">Kategori</th>
                <th style="width: 12%;">Lokasi</th>
                <th style="width: 25%;">Deskripsi</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Progress</th>
                <th style="width: 14%;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($aspirations as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->student->username ?? 'N/A' }}</td>
                <td>{{ $item->category->category_name ?? '-' }}</td>
                <td>{{ $item->location }}</td>
                <td>{{ Str::limit($item->description, 80) }}</td>
                <td>
                    <span class="badge badge-{{ $item->submission_status }}">
                        {{ $item->submission_status }}
                    </span>
                </td>
                <td>
                    @if ($item->aspiration)
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
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 30px; color: #94a3b8;">Tidak ada data aspirasi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Pengaduan Sarana & Prasarana</p>
        <p>Total data: {{ count($aspirations) }} aspirasi</p>
    </div>
</body>
</html>
