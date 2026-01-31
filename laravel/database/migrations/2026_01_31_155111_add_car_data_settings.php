<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            'starting_distance_counter' => 0,
            'distance_unit' => 'Kilometers',
        ];

        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                [
                    'group' => 'general',
                    'name' => $key,
                ],
                [
                    'payload' => json_encode($value),
                    'locked' => false,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->where('group', 'general')
            ->whereIn('name', ['starting_distance_counter', 'distance_unit'])
            ->delete();
    }
};
