<?php

namespace App\Exports;

use App\Models\InputAspirations;
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
        // Export data sesuai dengan filter yang sedang aktif di layar Admin
        return InputAspirations::query()
            ->with(['category', 'student', 'aspiration'])
            ->filter($this->filters);
    }

    public function headings(): array
    {
        return [
            'ID Input',
            'Nama Siswa',
            'Kategori',
            'Lokasi',
            'Deskripsi',
            'Status Pengajuan',
            'Status Progress',
            'Tanggal Masuk',
        ];
    }

    public function map($input): array
    {
        return [
            $input->id_input,
            $input->student->username ?? 'N/A',
            $input->category->category_name,
            $input->location,
            $input->description,
            $input->submission_status,
            $input->aspiration->progress_status ?? 'Belum Diproses',
            $input->created_at->format('d-m-Y'),
        ];
    }
}
