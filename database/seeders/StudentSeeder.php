<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Student::firstOrCreate([
            'nis' => 1234567891,
            'username' => 'kiwil',
            'password' => Hash::make('kiwil123'),
            'class' => 'xi rpl 3'
        ]);

        Student::firstOrCreate([
            'nis' => 1212121212,
            'username' => 'sapik',
            'password' => Hash::make('sapik123'),
            'class' => 'xii rpl 3'
        ]);

        Student::factory()->count(23)->create();
    }
}
