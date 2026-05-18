<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('input_aspirations') && !Schema::hasColumn('input_aspirations', 'title')) {
            Schema::table('input_aspirations', function (Blueprint $table) {
                $table->string('title', 150)->nullable()->after('submission_status');
            });
        }

        // Backfill title for legacy rows.
        DB::statement("UPDATE input_aspirations SET title = LEFT(description, 150) WHERE title IS NULL");

        // Legacy reviewed items go back to waiting for admin decision.
        DB::statement("UPDATE input_aspirations SET submission_status = 'menunggu' WHERE submission_status = 'reviewed'");
        DB::statement("ALTER TABLE input_aspirations MODIFY submission_status ENUM('menunggu','ditolak','diterima') NOT NULL DEFAULT 'menunggu'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE input_aspirations MODIFY submission_status ENUM('menunggu','reviewed','ditolak','diterima') NOT NULL DEFAULT 'menunggu'");

        if (Schema::hasColumn('input_aspirations', 'title')) {
            Schema::table('input_aspirations', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
    }
};
