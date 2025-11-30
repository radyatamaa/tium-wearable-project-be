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
        Schema::table('emergency_report_general_purposes', function (Blueprint $table) {
            $table->index(
                [
                    'user_customer_id',
                    'main_symptom',
                    'user_general_purpose_id',
                    'assortment',
                    'notification_time'
                ],
                'emergency_report_general_purposes_composite_index'
            );
        });

        Schema::table('emergency_report_public_welfares', function (Blueprint $table) {
            $table->index(
                [
                    'user_customer_id',
                    'main_symptom',
                    'user_general_purpose_id',
                    'assortment',
                    'notification_time'
                ],
                'emergency_report_public_welfare_composite_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergency_report_general_purposes', function (Blueprint $table) {
            $table->dropIndex('emergency_report_general_purposes_composite_index');
        });

        Schema::table('emergency_report_public_welfares', function (Blueprint $table) {
            $table->dropIndex('emergency_report_public_welfare_composite_index');
        });
    }
};