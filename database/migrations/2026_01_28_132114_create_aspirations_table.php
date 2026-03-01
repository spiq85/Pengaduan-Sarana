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
        Schema::create('aspirations', function (Blueprint $table) {
            $table->id('id_aspiration');

            $table->foreignId('id_input')
                ->constrained('input_aspirations', 'id_input')
                ->cascadeOnDelete();

            $table->foreignId('input_by')
                ->constrained('students', 'id_student')
                ->cascadeOnDelete();

            $table->foreignId('id_category')
                ->constrained('categories', 'id_category')
                ->cascadeOnDelete();

            $table->string('location', 100);
            $table->text('description');

            $table->foreignId('validated_by')
                ->constrained('users', 'id_user')
                ->cascadeOnDelete();

            $table->date('validated_at');

            // Progress
            $table->enum('progress_status', [
                'Belum Dimulai',
                'Dalam Proses',
                'Selesai'
            ])->default('Belum Dimulai');

            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->timestamp('deadline')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspirations');
    }
};
