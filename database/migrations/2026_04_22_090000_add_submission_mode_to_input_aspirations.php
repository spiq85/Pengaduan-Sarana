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
        if (!Schema::hasColumn('input_aspirations', 'submission_mode')) {
            Schema::table('input_aspirations', function (Blueprint $table) {
                $table->enum('submission_mode', ['template', 'custom'])
                    ->default('template')
                    ->after('submission_status');
            });
        }

        DB::statement("UPDATE input_aspirations SET submission_mode = 'template' WHERE submission_mode IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('input_aspirations', 'submission_mode')) {
            Schema::table('input_aspirations', function (Blueprint $table) {
                $table->dropColumn('submission_mode');
            });
        }
    }
};
