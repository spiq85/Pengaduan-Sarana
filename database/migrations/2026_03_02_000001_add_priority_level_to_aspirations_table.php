<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aspirations', function (Blueprint $table) {
            $table->string('priority_level', 20)->default('Normal')->after('progress_status');
        });
    }

    public function down(): void
    {
        Schema::table('aspirations', function (Blueprint $table) {
            $table->dropColumn('priority_level');
        });
    }
};
