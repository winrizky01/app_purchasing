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
        Schema::create('menu_sub_childrens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_parent_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('menu_children_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('displayname')->nullable();
            $table->string('pathmenu')->nullable();
            $table->string('icon')->nullable();
            $table->string('description')->nullable();
            $table->text('seo');
            $table->integer('position')->nullable();
            $table->enum('status', ['active', 'inactive']);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

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
        Schema::dropIfExists('menu_sub_childrens');
    }
};
