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
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('order_item_id');
            $table->unsignedInteger('customer_order_id')->default(0);
            $table->string('type')->nullable();
            $table->unsignedInteger('item_id')->default(0);
            $table->unsignedInteger('marka_id')->default(0);
            $table->string('vakkal_number')->nullable();
            $table->unsignedInteger('chamber_id')->nullable();
            $table->unsignedInteger('floor_id')->default(0)->nullable();
            $table->unsignedInteger('grid_id')->default(0)->nullable();
            $table->decimal('weight', 15, 2)->unsigned()->default(0)->nullable()->comment('in kg');
            $table->unsignedInteger('quantity')->default(0)->nullable();
            $table->unsignedInteger('no_of_days')->default(0)->nullable()->comment('for outward');
            $table->decimal('rate', 15, 2)->unsigned()->default(0)->nullable()->comment('Cooling Charge Rate(per month/kg)');
            $table->timestamps();

            $table->index('customer_order_id', 'customer_order_id');
            $table->index('item_id', 'item_id');
            $table->index('marka_id', 'marka_id');
            $table->index('floor_id', 'floor_id');
            $table->index('chamber_id', 'chamber_id');
            $table->index('vakkal_number', 'vakkal_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
