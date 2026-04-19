<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->index(['category', 'name'], 'menu_items_category_name_index');
            $table->index('is_available', 'menu_items_is_available_index');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex('menu_items_category_name_index');
            $table->dropIndex('menu_items_is_available_index');
        });
    }
};
