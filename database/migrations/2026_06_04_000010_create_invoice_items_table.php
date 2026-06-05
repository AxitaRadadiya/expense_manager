<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('unit_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
