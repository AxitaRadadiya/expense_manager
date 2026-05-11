<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_receives', function (Blueprint $table) {
            $table->id();
            $table->enum('payment_type', ['cash','online','cheque'])->default('cash');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('project_id');
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_receives');
    }
};
