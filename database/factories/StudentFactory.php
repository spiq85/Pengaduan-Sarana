<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            // Generate NIS unik 10 digit
            'nis' => $this->faker->unique()->numerify('##########'), 
            'username' => $this->faker->unique()->userName(),
            'password' => Hash::make('password123'),
            // Acak kelas dari daftar yang ada
            'class' => $this->faker->randomElement(['XI RPL 1', 'XI RPL 2', 'XI RPL 3', 'XII RPL 1', 'XII RPL 2']),
        ];
    }
}