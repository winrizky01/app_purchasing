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
        Schema::table('products', function (Blueprint $table) {
            $table->text('photo')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->double('last_price')->default(0)->change();
            $table->double('stock')->default(0)->change();
            $table->renameColumn('SKU', 'sku');
            $table->enum('is_inventory', ['yes','no']);
            $table->text('dimension')->nullable();
            $table->text('part_number')->nullable();
            $table->unsignedBigInteger('machine_id')->nullable();
            $table->text('spesification')->nullable();

            $table->foreign('machine_id')->references('id')->on('generals')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['machine_id']);

            $table->renameColumn('SKU', 'sku');
            $table->dropColumn('is_inventory');
            $table->dropColumn(['machine_id']);
        });
    }
};
