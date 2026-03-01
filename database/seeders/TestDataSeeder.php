<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Category;
use App\Models\InputAspirations;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Categories (Sesuai permintaan: bukan lokasi, tapi jenis layanan)
        $categories = [
            ['category_name' => 'Sarana', 'description' => 'Terkait peralatan penunjang belajar (AC, Proyektor, Kursi, dll)'],
            ['category_name' => 'Prasarana', 'description' => 'Terkait fisik bangunan (Atap bocor, pintu rusak, lantai pecah)'],
            ['category_name' => 'Kebersihan', 'description' => 'Terkait kebersihan lingkungan sekolah'],
            ['category_name' => 'Keamanan', 'description' => 'Terkait keamanan di lingkungan sekolah'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // 2. Seed Students (Contoh data siswa untuk testing login siswa)
        $student = Student::create([
            'nis' => 12345678,
            'username' => 'siswa_tester',
            'password' => Hash::make('password'), // password login: password
            'class' => 'XII-RPL-1',
        ]);

        // 3. Seed Input Aspirations (Contoh data aspirasi masuk)
        $dataAspirasi = [
            [
                'input_by' => $student->id_student,
                'id_category' => 1, // Sarana
                'input_at' => Carbon::now(),
                'submission_status' => 'menunggu',
                'location' => 'Lab Komputer 2',
                'description' => 'AC di lab tidak dingin, mohon dicek.',
            ],
            [
                'input_by' => $student->id_student,
                'id_category' => 2, // Prasarana
                'input_at' => Carbon::now()->subDays(1),
                'submission_status' => 'reviewed',
                'location' => 'Kantin Belakang',
                'description' => 'Atap kantin ada yang bocor saat hujan deras.',
            ],
            [
                'input_by' => $student->id_student,
                'id_category' => 3, // Kebersihan
                'input_at' => Carbon::now()->subDays(2),
                'submission_status' => 'diterima',
                'location' => 'Toilet Lantai 2',
                'description' => 'Tempat sampah di toilet pria sudah penuh dan berbau.',
                'admin_message' => 'Sudah ditindaklanjuti oleh petugas kebersihan.',
            ]
        ];

        foreach ($dataAspirasi as $aspiration) {
            InputAspirations::create($aspiration);
        }
    }
}