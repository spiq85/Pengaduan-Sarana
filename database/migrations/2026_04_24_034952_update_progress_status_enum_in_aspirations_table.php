<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check current database driver
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE aspirations MODIFY COLUMN progress_status ENUM('Belum Dimulai', 'Dalam Proses', 'Menunggu Konfirmasi Selesai', 'Selesai') DEFAULT 'Belum Dimulai'");
        } else {
            // For SQLite or others, we might need a different approach, 
            // but usually enums are just strings in SQLite.
            Schema::table('aspirations', function (Blueprint $table) {
                $table->string('progress_status')->default('Belum Dimulai')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE aspirations MODIFY COLUMN progress_status ENUM('Belum Dimulai', 'Dalam Proses', 'Selesai') DEFAULT 'Belum Dimulai'");
        }
    }
};
