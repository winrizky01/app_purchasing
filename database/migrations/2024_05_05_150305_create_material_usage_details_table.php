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
        Schema::create('material_usage_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("material_usage_id")->nullable();
            $table->unsignedBigInteger("material_request_id")->nullable();
            $table->unsignedBigInteger("material_request_detail_id")->nullable();
            $table->unsignedBigInteger("product_id")->nullable();
            $table->double("qty")->default(0);
            $table->text("notes")->nullable();
            $table->timestamps();

            $table->foreign('material_usage_id')->references('id')->on('material_usages')->cascadeOnDelete();
            $table->foreign('material_request_id')->references('id')->on('material_requests')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_usage_details');
    }
};
