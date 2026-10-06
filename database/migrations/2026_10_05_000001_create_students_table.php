<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('matric_no')->unique();
            $table->string('program')->nullable();
            $table->string('level')->nullable();
            $table->string('college')->nullable();
            $table->timestamp('admitted_at')->nullable();
            $table->timestamps();

            $table->index(['matric_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
