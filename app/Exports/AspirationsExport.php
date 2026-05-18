<?php

namespace App\Exports;

use App\Services\Aspiration\AspirationQueryService;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AspirationsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        return (new AspirationQueryService())
            ->buildAdminInputQuery($this->filters, null);
    }

    public function headings(): array
    {
        return [
            'ID Input',
            'Judul',
            'Nama Siswa',
            'Kategori',
            'Lokasi',
            'Deskripsi',
            'Rating (1-5)',
            'Feedback Siswa',
            'Status Pengajuan',
            'Status Progress',
            'Tanggal Masuk',
        ];
    }

    public function map($input): array
    {
        return [
            $input->id_input,
            $input->title,
            $input->student->username ?? 'N/A',
            $input->category->category_name,
            $input->location,
            $input->description,
            $input->rating ?? '-',
            $input->feedback ?? '-',
            $input->submission_status,
            $input->aspiration->progress_status ?? 'Belum Diproses',
            $input->created_at->format('d-m-Y'),
        ];
    }
}
