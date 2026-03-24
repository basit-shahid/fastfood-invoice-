<?php
// database/migrations/2024_01_01_000004_create_daily_reports_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyReportsTable extends Migration
{
    public function up()
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->integer('total_orders');
            $table->decimal('total_revenue', 10, 2);
            $table->decimal('average_order_value', 10, 2);
            $table->json('top_items')->nullable();
            $table->json('hourly_breakdown')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_reports');
    }
}