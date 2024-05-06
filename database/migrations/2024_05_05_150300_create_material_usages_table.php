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
        Schema::create('material_usages', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->dateTime("usage_date");
            $table->unsignedBigInteger("department_id")->nullable();
            $table->unsignedBigInteger("division_id")->nullable();
            $table->unsignedBigInteger("warehouse_id")->nullable();
            $table->text("description");
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('created_by');         
            $table->unsignedBigInteger('updated_by');         
            $table->unsignedBigInteger('deleted_by');         
            
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete(); // Tambahkan foreign key untuk warehouse_id
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_usages');
    }
};
