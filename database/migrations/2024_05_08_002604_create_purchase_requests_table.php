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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string("code")->nullable();
            $table->string("purchase_request_no")->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete();
            $table->double("revision")->nullable;
            $table->dateTime('effective_date')->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('document_status_id')->nullable();
            $table->string("notes")->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->unsignedBigInteger('riviewed_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();

            $table->unsignedBigInteger('riviewed_at')->nullable();
            $table->unsignedBigInteger('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('document_status_id')->references('id')->on('generals')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('riviewed_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('deleted_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
