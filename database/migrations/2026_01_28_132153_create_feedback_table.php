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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id('id_feedback');

            $table->foreignId('id_aspiration')
                ->constrained('aspirations', 'id_aspiration')
                ->cascadeOnDelete();

            $table->text('message');

            $table->foreignId('feedback_by')
                ->constrained('users', 'id_user')
                ->cascadeOnDelete();

            $table->date('feedback_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
