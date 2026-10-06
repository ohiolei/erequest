<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('staff_number')->unique();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('employment_type')->nullable();
            $table->timestamp('employed_at')->nullable();
            $table->timestamps();

            $table->index(['staff_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
