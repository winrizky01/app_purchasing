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
        Schema::table('material_requests', function (Blueprint $table) {
            $table->dropColumn(['date']);
            $table->datetime('request_date')->nullable()->change();

            // $table->unsignedBigInteger('riviewed_by')->nullable();
            // $table->unsignedBigInteger('riviewed_at')->nullable();
            // $table->unsignedBigInteger('approved_by')->nullable();
            // $table->unsignedBigInteger('approved_at')->nullable();

            // $table->foreign('riviewed_by')->references('id')->on('users')->cascadeOnDelete();
            // $table->foreign('approved_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_requests', function (Blueprint $table) {
            // $table->dropForeign(['riviewed_by']);
            // $table->dropForeign(['approved_by']);

            // $table->dropColumn(['riviewed_by']);
            // $table->dropColumn(['riviewed_at']);
            // $table->dropColumn(['approved_by']);
            // $table->dropColumn(['approved_at']);
            $table->dropColumn(['date']);
            $table->datetime('request_date')->nullable()->change();



        });
    }
};
