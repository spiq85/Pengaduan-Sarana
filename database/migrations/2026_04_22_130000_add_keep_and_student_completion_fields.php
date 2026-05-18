<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('input_aspirations', function (Blueprint $table) {
            if (!Schema::hasColumn('input_aspirations', 'is_kept')) {
                $table->boolean('is_kept')->default(false)->after('submission_mode');
            }
            if (!Schema::hasColumn('input_aspirations', 'kept_until')) {
                $table->dateTime('kept_until')->nullable()->after('is_kept');
            }
            if (!Schema::hasColumn('input_aspirations', 'kept_note')) {
                $table->text('kept_note')->nullable()->after('kept_until');
            }
        });

        Schema::table('aspirations', function (Blueprint $table) {
            if (!Schema::hasColumn('aspirations', 'student_confirmed_done_at')) {
                $table->dateTime('student_confirmed_done_at')->nullable()->after('end_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('aspirations', function (Blueprint $table) {
            if (Schema::hasColumn('aspirations', 'student_confirmed_done_at')) {
                $table->dropColumn('student_confirmed_done_at');
            }
        });

        Schema::table('input_aspirations', function (Blueprint $table) {
            if (Schema::hasColumn('input_aspirations', 'kept_note')) {
                $table->dropColumn('kept_note');
            }
            if (Schema::hasColumn('input_aspirations', 'kept_until')) {
                $table->dropColumn('kept_until');
            }
            if (Schema::hasColumn('input_aspirations', 'is_kept')) {
                $table->dropColumn('is_kept');
            }
        });
    }
};
