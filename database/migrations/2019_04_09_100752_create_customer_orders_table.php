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
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->increments('customer_order_id');
            $table->unsignedInteger('customer_id')->default(0);
            $table->bigInteger('sr_no')->default(0);
            $table->string('transporter')->nullable()->comment('for inward');
            $table->string('from')->nullable()->comment('for inward');
            $table->string('type')->nullable();
            $table->dateTime('date');
            $table->string('vehicle')->nullable();
            $table->string('order_by')->nullable()->comment('for outward');
            $table->text('address')->nullable()->comment('Delivery Address for outward');
            $table->decimal('additional_charge', 15, 2)->unsigned()->default(0)->nullable()->comment('Additional Charges for outward');
            $table->timestamps();

            $table->index('customer_id', 'customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_orders');
    }
};
