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
        Schema::table('vendors', function (Blueprint $table) {
            $table->enum('tax', ['yes','no'])->nullable();
            $table->string('npwp')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->unsignedBigInteger('payment_terms_id')->nullable();

            $table->foreign('payment_terms_id')->references('id')->on('generals')->cascadeOnDelete();
            $table->foreign('bank_account_id')->references('id')->on('generals')->cascadeOnDelete();
            $table->foreign('payment_method_id')->references('id')->on('generals')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign(['bank_account_id']);
            $table->dropForeign(['payment_method_id']);
            $table->dropForeign(['payment_terms_id']);

            $table->dropColumn(['tax']);
            $table->dropColumn(['npwp']);
            $table->dropColumn(['bank_account_id']);
            $table->dropColumn(['bank_account_number']);
            $table->dropColumn(['payment_method_id']);
            $table->dropColumn(['payment_terms_id']);
        });
    }
};
