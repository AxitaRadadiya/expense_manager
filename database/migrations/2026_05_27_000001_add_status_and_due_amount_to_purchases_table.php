<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('due_amount', 14, 2)->default(0)->after('amount');
            $table->string('status', 20)->default('pending')->after('due_amount');
        });

        // Populate existing rows: set due_amount = amount and status based on amount
        DB::table('purchases')->update([
            'due_amount' => DB::raw('amount'),
            'status' => DB::raw("CASE WHEN amount <= 0 THEN 'paid' ELSE 'pending' END"),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('purchases', 'due_amount')) {
                $table->dropColumn('due_amount');
            }
        });
    }
};
