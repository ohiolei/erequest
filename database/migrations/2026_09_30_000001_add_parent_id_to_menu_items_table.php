<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->uuid('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('menu_items')->cascadeOnDelete();
            $table->string('route')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::table('menu_items')->whereNull('route')->exists()) {
            throw new \RuntimeException(
                'Cannot roll back the menu parent migration while menu items have a null route. Assign routes before rolling back.'
            );
        }

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
            $table->string('route')->nullable(false)->change();
        });
    }
};
