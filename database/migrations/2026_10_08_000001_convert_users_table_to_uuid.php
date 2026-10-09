<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $type = Schema::hasColumn('users', 'id')
            ? strtolower(Schema::getColumnType('users', 'id', true))
            : '';

        if (! preg_match('/\b(string|char|varchar|uuid)\b/', $type)) {
            throw new \RuntimeException(
                'The users.id column must already be a UUID string. Converting it in place would break existing foreign-key relationships.'
            );
        }
    }

    public function down(): void
    {
        // User IDs remain UUIDs; reverting them would invalidate related records.
    }
};
