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
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('item_rate', 15, 2)->unsigned()->default(0)->nullable()->comment('Item Rate')->after('grid_id');
            $table->decimal('insurance_rate', 15, 2)->unsigned()->default(0)->nullable()->comment('Insurance Rate (per month)')->after('item_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('item_rate');
            $table->dropColumn('insurance_rate');
        });
    }
};
