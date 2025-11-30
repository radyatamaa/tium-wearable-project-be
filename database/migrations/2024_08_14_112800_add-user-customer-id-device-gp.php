<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('devices_smart_watch_general_purposes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_customer_id')->nullable();
            $table->foreign('user_customer_id')->references('id')->on('user_customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices_smart_watch_general_purposes', function (Blueprint $table) {
            $table->dropColumn('user_customer_id');
        });
    }
};