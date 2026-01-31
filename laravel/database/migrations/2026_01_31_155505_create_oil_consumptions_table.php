<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('oil_consumptions', function (Blueprint $table) {
            $table->id();
            $table->date('fill_date');
            $table->float('fill_amount');
            $table->float('fill_price');
            $table->integer('current_distance_counter');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oil_consumptions');
    }
};
