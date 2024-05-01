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
        Schema::create('adjustment_stock_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adjustment_stock_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('stock_type_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->double("qty")->default(0);
            $table->text("notes");
            $table->timestamps();

            $table->foreign('stock_type_id')->references('id')->on('generals')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adjustment_stock_details');
    }
};
