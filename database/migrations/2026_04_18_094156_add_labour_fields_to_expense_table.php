<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('total_labour', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('expense', function (Blueprint $table) {
            $table->dropColumn(['vendor_id', 'start_date', 'end_date', 'total_labour']);
        });
    }
};
