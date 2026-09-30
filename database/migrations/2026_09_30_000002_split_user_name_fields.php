<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('fname')->nullable();
            $table->string('mname')->nullable();
            $table->string('lname')->nullable();
        });

        DB::table('users')->select('id', 'name')->orderBy('id')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $parts = preg_split('/\s+/u', trim($user->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
                $fname = array_shift($parts);
                $lname = $parts ? array_pop($parts) : null;

                DB::table('users')->where('id', $user->id)->update([
                    'fname' => $fname,
                    'mname' => $parts ? implode(' ', $parts) : null,
                    'lname' => $lname,
                ]);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable();
        });

        DB::table('users')->select('id', 'fname', 'mname', 'lname')->orderBy('id')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $name = implode(' ', array_filter([
                    $user->fname,
                    $user->mname,
                    $user->lname,
                ], fn ($part) => is_string($part) && trim($part) !== ''));

                DB::table('users')->where('id', $user->id)->update(['name' => $name]);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fname', 'mname', 'lname']);
            $table->string('name')->nullable(false)->change();
        });
    }
};