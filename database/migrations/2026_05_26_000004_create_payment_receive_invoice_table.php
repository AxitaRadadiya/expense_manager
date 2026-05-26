<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_receive_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_receive_id');
            $table->unsignedBigInteger('invoice_id');
            $table->decimal('amount', 15, 2);
            $table->timestamps();

            $table->foreign('payment_receive_id')->references('id')->on('payment_receives')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_receive_invoices');
    }
};
