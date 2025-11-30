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
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('admin_level');
            $table->string('admin_id');
            $table->string('admin_name');
            $table->date('registration_date');
            $table->string('announcement_title')->nullable();
            $table->text('Announcement_details')->nullable();
            $table->string('service_usage_fee_the_entire')->nullable();
            $table->string('service_usage_fee_exhibit_on')->nullable();
            $table->string('service_usage_fee_suspension_of_exhibition')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};