<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->date('execution_date')->nullable()->after('amount');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->date('execution_date')->nullable()->after('amount');
        });

        // Update existing records to use created_at date
        DB::statement('UPDATE incomes SET execution_date = DATE(created_at) WHERE execution_date IS NULL');
        DB::statement('UPDATE expenses SET execution_date = DATE(created_at) WHERE execution_date IS NULL');

        // Make the field required after setting values
        Schema::table('incomes', function (Blueprint $table) {
            $table->date('execution_date')->nullable(false)->change();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->date('execution_date')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn('execution_date');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('execution_date');
        });
    }
};
