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
        Schema::create('mr_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_material_request_id')->nullable();
            $table->unsignedBigInteger('type_material_request')->nullable();
            $table->string('code')->nullable();
            $table->datetime('request_date')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('division_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('justification')->nullable();
            $table->unsignedBigInteger('remark_id')->nullable();
            $table->text('document_photo')->nullable();
            $table->text('document_pdf')->nullable();
            $table->text('last_reason')->nullable();
            $table->timestamps();
            $table->datetime('revisied_at')->nullable();
            $table->unsignedBigInteger('revisied_by')->nullable();

            $table->foreign('remark_id')->references('id')->on('generals')->cascadeOnDelete();
            $table->foreign('revisied_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_request_histories');
    }
};
