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
            $table->unsignedBigInteger('document_status_id')->nullable();
            $table->text('document_photo')->nullable();
            $table->text('document_pdf')->nullable();

            $table->foreign('document_status_id')->references('id')->on('generals')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_requests', function (Blueprint $table) {
            $table->dropForeign(['document_status_id']);

            $table->dropColumn(['document_status_id']);
            $table->dropColumn(['document_photo']);
            $table->dropColumn(['document_pdf']);
        });
    }
};
