<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('input_aspirations', function (Blueprint $table) {
            $table->id('id_input');

            $table->foreignId('input_by')
                ->constrained('students', 'id_student')
                ->cascadeOnDelete();

            $table->foreignId('id_category')
                ->constrained('categories', 'id_category')
                ->cascadeOnDelete();

            $table->dateTime('input_at');
            $table->string('image');

            $table->enum('submission_status', [
                'menunggu',
                'reviewed',
                'ditolak',
                'diterima'
            ])->default('menunggu');

            $table->string('location', 100);
            $table->text('description');
            $table->text('admin_message')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('input_aspirations');
    }
};
