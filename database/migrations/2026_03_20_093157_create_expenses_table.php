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
        Schema::create('expense', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projects_id')->constrained()->cascadeOnDelete();
            $table->foreignId('users_id')->constrained()->cascadeOnDelete();
            $table->date('expense_date');
            $table->string('category')->nullable();
            $table->string('sub_category')->nullable();
            $table->decimal('amount', 12, 2);
            $table->text('description');
            $table->string('bill_path');          
            $table->string('bill_original_name'); 
            $table->enum('payment_mode', ['cash', 'bank_transfer', 'online', 'cheque'])->nullable();
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense');
    }
};
