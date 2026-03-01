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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aspiration_id');
            $table->unsignedBigInteger('student_id');

            $table->text('body');
            $table->timestamps();

            // Foreign Key
            $table->foreign('aspiration_id')->references('id_aspiration')->on('aspirations')->onDelete('cascade');
            $table->foreign('student_id')->references('id_student')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
