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
        Schema::create('marka', function (Blueprint $table) {
            $table->increments('marka_id');
            $table->unsignedInteger('item_id')->default(0);
            $table->string('name')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('item_id', 'item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marka');
    }
};
