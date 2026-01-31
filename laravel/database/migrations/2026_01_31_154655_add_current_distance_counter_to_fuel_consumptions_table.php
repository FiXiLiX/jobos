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
        Schema::table('fuel_consumptions', function (Blueprint $table) {
            $table->integer('current_distance_counter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fuel_consumptions', function (Blueprint $table) {
            $table->dropColumn('current_distance_counter');
        });
    }
};
