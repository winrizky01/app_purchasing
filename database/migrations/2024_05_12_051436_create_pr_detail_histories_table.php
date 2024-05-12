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
        Schema::create('pr_detail_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pr_history_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->cascadeOnDelete();
            $table->double('qty')->nullable();
            $table->string('description')->nullable();
            $table->string('identity_required_date')->nullable();
            $table->unsignedBigInteger('document_status_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_detail_histories');
    }
};
