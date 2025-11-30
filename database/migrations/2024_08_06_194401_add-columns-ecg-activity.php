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
        Schema::table('ecg_activities', function (Blueprint $table) {
            $table->float('speed')->nullable();
            $table->float('frequency')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ecg_activities', function (Blueprint $table) {
            $table->dropColumn('speed');
            $table->dropColumn('frequency');
        });
    }
};