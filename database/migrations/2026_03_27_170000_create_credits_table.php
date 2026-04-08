<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projects_id')->constrained()->cascadeOnDelete();
            $table->foreignId('users_id')->constrained()->cascadeOnDelete();
            $table->date('credit_date');
            $table->string('category')->nullable();
            $table->string('sub_category')->nullable();
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->text('note')->nullable();
            $table->string('bill_path')->nullable();
            $table->string('bill_original_name')->nullable();
            $table->enum('payment_mode', ['cash', 'online', 'cheque'])->nullable();
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit');
    }
};
