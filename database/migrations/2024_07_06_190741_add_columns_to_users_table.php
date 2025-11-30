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
        Schema::table('users', function (Blueprint $table) {
            $table->string('status_of_usage_inquiries_unconfirmed')->nullable();
            $table->string('status_of_usage_inquiries_receiving')->nullable();
            $table->string('status_of_usage_inquiries_answer_completed')->nullable();
            $table->date('registration_date')->nullable();
            $table->date('last_login')->nullable();
            $table->string('administrator_level')->nullable();
            $table->string('position')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('photo');
            $table->dropColumn('status_of_usage_inquiries_unconfirmed');
            $table->dropColumn('status_of_usage_inquiries_receiving');
            $table->dropColumn('status_of_usage_inquiries_answer_completed');
            $table->dropColumn('registration_date');
            $table->dropColumn('last_login');
            $table->dropColumn('administrator_level');
            $table->dropColumn('position');
            $table->dropColumn('phone_number');
            $table->dropColumn('email');
        });
    }
};