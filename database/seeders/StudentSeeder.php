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
        $students = [
            ['nis' => 1234567891, 'username' => 'kiwil', 'password' => Hash::make('kiwil123'), 'class' => 'X RPL 3'],
            ['nis' => 1212121212, 'username' => 'sapik', 'password' => Hash::make('sapik123'), 'class' => 'XII RPL 3'],
            ['nis' => 1111111111, 'username' => 'russ21', 'password' => Hash::make('russ123'), 'class' => 'XI TJKT 1'],
            ['nis' => 2222222222, 'username' => 'jaka_pancing', 'password' => Hash::make('jaka123'), 'class' => 'X PSPT 3'],
            ['nis' => 3333333333, 'username' => 'maya_putri', 'password' => Hash::make('maya123'), 'class' => 'XI ANIM 1'],
            ['nis' => 4444444444, 'username' => 'budi_santoso', 'password' => Hash::make('budi123'), 'class' => 'X TE 1'],
            ['nis' => 5555555555, 'username' => 'siti_nurhaliza', 'password' => Hash::make('siti123'), 'class' => 'XI RPL 2'],
            ['nis' => 6666666666, 'username' => 'hendra_wijaya', 'password' => Hash::make('hendra123'), 'class' => 'X ANIM 1'],
        ];

        foreach ($students as $student) {
            Student::firstOrCreate(
                ['username' => $student['username']],
                $student
            );
        }
        
        $this->command->info('StudentSeeder: ' . count($students) . ' students created/updated.');
    }
}
