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
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->default(0)->nullable()->after('customer_id');
            $table->unsignedInteger('deleted_user_id')->default(0)->nullable()->after('order_by');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('deleted_user_id');
            $table->dropSoftDeletes();
        });
    }
};
