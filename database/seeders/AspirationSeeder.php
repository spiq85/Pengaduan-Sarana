<?php

namespace Database\Seeders;

use App\Models\Aspirations;
use App\Models\Category;
use App\Models\InputAspirations;
use App\Models\Location;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AspirationSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::query()->get()->keyBy('username');
        $categories = Category::query()->get()->keyBy('category_name');
        $admin = User::query()->where('username', 'admin')->first();
        $defaultLocationId = Location::query()->orderBy('id_location')->value('id_location');

        if ($students->isEmpty() || $categories->isEmpty() || !$defaultLocationId) {
            $this->command?->warn('AspirationSeeder skipped: students, categories, or locations are missing.');
            return;
        }

        $completedItems = [
            [
                'mode' => 'template',
                'student' => 'kiwil',
                'title' => 'AC Ruang Kelas 1 Sudah Dingin Kembali',
                'description' => 'Unit AC di ruang kelas 1 sudah diperbaiki dan kembali berfungsi normal.',
                'category' => 'Sarana',
                'location' => 'Ruang Kelas 1',
                'submission_at' => Carbon::now()->subDays(18)->setTime(8, 10),
                'progress_status' => 'Selesai',
                'rating' => 5,
                'feedback' => 'Sekarang kelas jadi lebih nyaman dan belajar lebih fokus.',
                'admin_message' => 'Aspirasi disetujui dan AC telah diganti sparepart-nya.',
            ],
            [
                'mode' => 'template',
                'student' => 'sapik',
                'title' => 'Tempat Sampah Baru di Kantin',
                'description' => 'Tempat sampah sudah ditambah dan area kantin lebih bersih dari sebelumnya.',
                'category' => 'Kebersihan',
                'location' => 'Kantin',
                'submission_at' => Carbon::now()->subDays(16)->setTime(9, 0),
                'progress_status' => 'Selesai',
                'rating' => 4,
                'feedback' => 'Kebersihan kantin meningkat dan tidak bau lagi.',
                'admin_message' => 'Tim kebersihan menambah dua tempat sampah baru di kantin.',
            ],
            [
                'mode' => 'template',
                'student' => 'russ21',
                'title' => 'CCTV Area Parkir Sudah Aktif',
                'description' => 'Pemasangan dan aktivasi CCTV area parkir telah selesai dilakukan.',
                'category' => 'Keamanan',
                'location' => 'Area Parkir',
                'submission_at' => Carbon::now()->subDays(14)->setTime(10, 30),
                'progress_status' => 'Selesai',
                'rating' => 5,
                'feedback' => 'Sekarang area parkir terasa lebih aman.',
                'admin_message' => 'CCTV area parkir sudah diuji dan dinyatakan aktif.',
            ],
            [
                'mode' => 'template',
                'student' => 'jaka_pancing',
                'title' => 'Komputer Laboratorium Sudah Diganti',
                'description' => 'Beberapa komputer lab yang rusak sudah diganti dan semua unit kembali normal.',
                'category' => 'Fasilitas IT',
                'location' => 'Laboratorium',
                'submission_at' => Carbon::now()->subDays(12)->setTime(7, 50),
                'progress_status' => 'Selesai',
                'rating' => 5,
                'feedback' => 'Praktikum jadi lancar lagi tanpa kendala.',
                'admin_message' => 'Komputer lab sudah dilakukan maintenance dan update software.',
            ],
            [
                'mode' => 'template',
                'student' => 'maya_putri',
                'title' => 'Atap Perpustakaan Sudah Diperbaiki',
                'description' => 'Kebocoran di area perpustakaan sudah ditangani dan atap kembali rapat.',
                'category' => 'Prasarana',
                'location' => 'Perpustakaan',
                'submission_at' => Carbon::now()->subDays(10)->setTime(11, 15),
                'progress_status' => 'Selesai',
                'rating' => 4,
                'feedback' => 'Ruang baca sekarang lebih nyaman saat hujan.',
                'admin_message' => 'Perbaikan atap perpustakaan telah selesai dilakukan.',
            ],
            [
                'mode' => 'custom',
                'student' => 'budi_santoso',
                'title' => 'Genteng Halaman Utama Sudah Diamankan',
                'description' => 'Genteng lepas di area halaman utama sudah diamankan dan area aman dilalui.',
                'category' => 'Prasarana',
                'location' => 'Halaman Utama Sekolah',
                'submission_at' => Carbon::now()->subDays(9)->setTime(8, 20),
                'progress_status' => 'Selesai',
                'rating' => 5,
                'feedback' => 'Penanganannya cepat dan rapi.',
                'admin_message' => 'Area genteng sudah diperbaiki oleh tim pemeliharaan.',
            ],
            [
                'mode' => 'custom',
                'student' => 'siti_nurhaliza',
                'title' => 'Ventilasi Ruang Guru Sudah Lancar',
                'description' => 'Ventilasi ruang guru sudah dibenahi sehingga sirkulasi udara jauh lebih baik.',
                'category' => 'Fasilitas IT',
                'location' => 'Ruang Guru Lt 2',
                'submission_at' => Carbon::now()->subDays(8)->setTime(9, 40),
                'progress_status' => 'Selesai',
                'rating' => 4,
                'feedback' => 'Ruang guru kini lebih nyaman untuk bekerja.',
                'admin_message' => 'Ventilasi ruang guru sudah ditingkatkan.',
            ],
            [
                'mode' => 'custom',
                'student' => 'hendra_wijaya',
                'title' => 'Tangga Darurat Sudah Dipelitur dan Aman',
                'description' => 'Tangga darurat yang licin sudah diperbaiki dan diberikan lapisan aman.',
                'category' => 'Keamanan',
                'location' => 'Tangga Darurat Gedung Belakang',
                'submission_at' => Carbon::now()->subDays(7)->setTime(7, 25),
                'progress_status' => 'Selesai',
                'rating' => 5,
                'feedback' => 'Sekarang tangga darurat jauh lebih aman.',
                'admin_message' => 'Perbaikan keamanan tangga darurat telah selesai.',
            ],
        ];

        foreach ($completedItems as $itemData) {
            $student = $students->get($itemData['student']);
            $category = $categories->get($itemData['category']);

            if (!$student || !$category) {
                continue;
            }

            $input = InputAspirations::updateOrCreate(
                [
                    'input_by' => $student->id_student,
                    'title' => $itemData['title'],
                ],
                [
                    'input_at' => $itemData['submission_at'],
                    'id_category' => $category->id_category,
                    'id_location' => $defaultLocationId,
                    'room_number' => null,
                    'submission_status' => 'diterima',
                    'submission_mode' => $itemData['mode'],
                    'location' => $itemData['location'],
                    'description' => $itemData['description'],
                    'admin_message' => $itemData['admin_message'],
                    'rating' => $itemData['rating'],
                    'feedback' => $itemData['feedback'],
                    'image' => null,
                    'created_at' => $itemData['submission_at'],
                    'updated_at' => now(),
                ]
            );

            Aspirations::updateOrCreate(
                [
                    'id_input' => $input->id_input,
                ],
                [
                    'input_by' => $student->id_student,
                    'id_category' => $category->id_category,
                    'location' => $itemData['location'],
                    'description' => $itemData['description'],
                    'validated_by' => $admin?->id_user,
                    'validated_at' => $itemData['submission_at']->copy()->addHours(1),
                    'progress_status' => 'Selesai',
                    'priority_level' => 'Normal',
                    'start_at' => $itemData['submission_at']->copy()->addHours(2),
                    'end_at' => $itemData['submission_at']->copy()->addDays(2),
                    'student_confirmed_done_at' => $itemData['submission_at']->copy()->addDays(2)->addHours(4),
                    'progress_evidence_image' => null,
                    'created_at' => $itemData['submission_at']->copy()->addHours(3),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command?->info('AspirationSeeder: ' . count($completedItems) . ' completed aspirations seeded across multiple categories.');
    }
}