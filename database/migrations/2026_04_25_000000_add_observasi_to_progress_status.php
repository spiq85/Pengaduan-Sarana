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
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE aspirations MODIFY COLUMN progress_status ENUM('Belum Dimulai', 'Dalam Proses', 'Menunggu Konfirmasi Selesai', 'Selesai', 'Observasi') DEFAULT 'Belum Dimulai'");
        } else {
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
            DB::statement("ALTER TABLE aspirations MODIFY COLUMN progress_status ENUM('Belum Dimulai', 'Dalam Proses', 'Menunggu Konfirmasi Selesai', 'Selesai') DEFAULT 'Belum Dimulai'");
        }
    }
};
