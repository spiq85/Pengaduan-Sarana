<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aspirations', function (Blueprint $table) {
            if (!Schema::hasColumn('aspirations', 'progress_evidence_image')) {
                $table->string('progress_evidence_image')->nullable()->after('deadline');
            }
        });
    }

    public function down(): void
    {
        Schema::table('aspirations', function (Blueprint $table) {
            if (Schema::hasColumn('aspirations', 'progress_evidence_image')) {
                $table->dropColumn('progress_evidence_image');
            }
        });
    }
};
