<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id('id_location');
            $table->string('location_name', 100)->unique();
            $table->enum('location_type', ['kelas', 'toilet', 'lab', 'perpustakaan', 'kantin', 'lapangan', 'koridor', 'lainnya']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default locations
        DB::table('locations')->insert([
            ['location_name' => 'Ruang Kelas', 'location_type' => 'kelas', 'is_active' => 1, 'created_at' => now()],
            ['location_name' => 'Toilet', 'location_type' => 'toilet', 'is_active' => 1, 'created_at' => now()],
            ['location_name' => 'Laboratorium', 'location_type' => 'lab', 'is_active' => 1, 'created_at' => now()],
            ['location_name' => 'Perpustakaan', 'location_type' => 'perpustakaan', 'is_active' => 1, 'created_at' => now()],
            ['location_name' => 'Kantin', 'location_type' => 'kantin', 'is_active' => 1, 'created_at' => now()],
            ['location_name' => 'Lapangan', 'location_type' => 'lapangan', 'is_active' => 1, 'created_at' => now()],
            ['location_name' => 'Koridor', 'location_type' => 'koridor', 'is_active' => 1, 'created_at' => now()],
            ['location_name' => 'Lainnya', 'location_type' => 'lainnya', 'is_active' => 1, 'created_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};