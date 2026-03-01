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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aspiration_id');
            $table->unsignedBigInteger('student_id');

            $table->timestamps();

            // Foreign keys
            $table->foreign('aspiration_id')->references('id_aspiration')->on('aspirations')->onDelete('cascade');
            $table->foreign('student_id')->references('id_student')->on('students')->onDelete('cascade');

            // Unik biar ga bisa nyepam vote
            $table->unique(['aspiration_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
