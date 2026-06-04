<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFirstLastNameToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
        });

        // Populate first_name and last_name from existing `name` column
        DB::table('users')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                if (empty($user->name)) {
                    continue;
                }

                $parts = preg_split('/\s+/', trim($user->name));
                $first = $parts[0] ?? null;
                $last = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : null;

                DB::table('users')->where('id', $user->id)->update([
                    'first_name' => $first,
                    'last_name' => $last,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
}
