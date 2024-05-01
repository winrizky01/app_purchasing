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
        // Schema::create('product_stocks', function (Blueprint $table) {
            // $table->id();
            // $table->unsignedBigInterger('warehouse_id')->nullable();
            // $table->unsignedBigInterger('stock_type_id')->nullable();
            // $table->unsignedBigInterger('stock_type_name')->nullable();
            // $table->unsignedBigInterger('product_id')->nullable();
            // $table->double('qty')->default(0);
            // $table->timestamps();

            // $table->unsignedBigInteger('created_by')->nullable();
            // $table->unsignedBigInteger('updated_by')->nullable();
            // $table->unsignedBigInteger('deleted_by')->nullable();

            // $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            // $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete();
            // $table->foreign('deleted_by')->references('id')->on('users')->cascadeOnDelete();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('product_stocks');
    }
};
