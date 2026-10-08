<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('new_id')->nullable()->after('id');
        });

        \App\Models\User::query()->each(function ($user) {
            $user->new_id = (string) Str::uuid();
            $user->save();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropPrimary('id');
            $table->dropColumn('id');
            $table->renameColumn('new_id', 'id');
            $table->primary('id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
        });
    }
};
