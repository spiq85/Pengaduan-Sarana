<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::firstOrCreate([
            'category_name' => 'Sarana',
            'description' => 'Sarana sekolah seperti meja, kursi, dll.'
        ]);
        Category::firstOrCreate([
            'category_name' => 'Kebersihan',
            'description' => 'Perlengkapan kebersihan seperti sapu, tempat sampah, dll.'
        ]);
        Category::firstOrCreate([
            'category_name' => 'Keamanan',
            'description' => 'Perlengkapan keamanan seperti CCTV, alarm, dll.'
        ]);
        Category::firstOrCreate([
            'category_name' => 'Fasilitas IT',
            'description' => 'Perlengkapan teknologi informasi seperti komputer, proyektor, dll.'
        ]);
        Category::firstOrCreate([
            'category_name' => 'Prasarana',
            'description' => 'Prasarana fisik seperti gedung, atap, dll.'
        ]);
    }
}
