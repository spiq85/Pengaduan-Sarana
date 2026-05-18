<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('input_aspirations', function (Blueprint $table) {
            $table->unsignedBigInteger('id_location')->nullable()->after('id_category');
            $table->string('room_number', 50)->nullable()->after('id_location');
            
            $table->foreign('id_location')
                ->references('id_location')
                ->on('locations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('input_aspirations', function (Blueprint $table) {
            $table->dropForeign(['id_location']);
            $table->dropColumn('id_location');
            $table->dropColumn('room_number');
        });
    }
};