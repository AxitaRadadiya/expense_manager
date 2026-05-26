<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('due_amount', 15, 2)->default(0)->after('amount');
        });

        // Set due_amount = amount for existing invoices
        DB::table('invoices')->update(['due_amount' => DB::raw('amount')]);
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('due_amount');
        });
    }
};
