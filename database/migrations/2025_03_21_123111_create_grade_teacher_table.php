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
        Schema::create('grade_teacher', function (Blueprint $table) {
            $table->foreignId('grade_id')->constrained();
            $table->foreignId('teacher_id')->constrained('users');
            $table->primary(['grade_id', 'teacher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_teacher');
    }
};
