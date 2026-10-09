<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('sessions', 'user_id')) {
            return;
        }

        $type = strtolower(Schema::getColumnType('sessions', 'user_id'));
        if (preg_match('/\b(string|char|varchar|uuid)\b/', $type)) {
            return;
        }

        Schema::table('sessions', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        throw new \RuntimeException(
            'The sessions.user_id UUID migration cannot be reversed safely while user IDs are UUIDs.'
        );
    }
};
