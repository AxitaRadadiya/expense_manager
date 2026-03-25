<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $users = DB::table('users')
            ->select('id', 'project_id')
            ->whereNotNull('project_id')
            ->get();

        foreach ($users as $user) {
            DB::table('project_user')->updateOrInsert(
                [
                    'project_id' => $user->project_id,
                    'user_id' => $user->id,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        // No-op: this migration only backfills relationship data.
    }
};
