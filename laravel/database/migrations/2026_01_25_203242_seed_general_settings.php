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
            'app_name' => 'Budget App',
            'app_currency' => 'USD',
            'default_currency' => 'USD',
            'notifications_enabled' => true,
            'active_currencies' => ['USD'],
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
            ->delete();
    }
};
