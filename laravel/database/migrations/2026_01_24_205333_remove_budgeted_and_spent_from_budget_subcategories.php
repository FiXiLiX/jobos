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
        Schema::table('budget_subcategories', function (Blueprint $table) {
            $table->dropColumn(['budgeted', 'spent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budget_subcategories', function (Blueprint $table) {
            $table->float('budgeted', 12, 2)->default(0);
            $table->float('spent', 12, 2)->default(0);
        });
    }
};
