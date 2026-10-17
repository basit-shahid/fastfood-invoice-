<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_item_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('mime_type', 64);
            $table->string('checksum', 64);
            // Base64 text keeps the payload portable across PostgreSQL/MySQL drivers.
            $table->longText('contents');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_images');
    }
};
