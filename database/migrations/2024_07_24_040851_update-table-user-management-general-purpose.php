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
        Schema::rename('user_management_report_general_purposes', 'user_management_general_purposes');
        Schema::table('user_management_general_purposes', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('position')->nullable();
            $table->string('unit')->nullable();
            $table->string('contact_information')->nullable();
            $table->string('emergency_contact_1')->nullable();
            $table->string('emergency_contact_2')->nullable();

            $table->unsignedBigInteger('user_customer_id')->nullable();
            $table->foreign('user_customer_id', 'um_c_cust_id_fk')
                ->references('id')->on('user_customers')->onDelete('cascade');

            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->foreign('user_general_purpose_id', 'um_gen_pur_id_fk')
                ->references('id')->on('user_general_purposes')->onDelete('cascade');

            $table->index('email');
        });

        Schema::table('user_customers', function (Blueprint $table) {
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('user_management_general_purposes', 'user_management_report_general_purposes');
        Schema::table('user_management_report_general_purposes', function (Blueprint $table) {
            $table->dropColumn('email');
            $table->dropColumn('birth_date');
            $table->dropColumn('position');
            $table->dropColumn('unit');
            $table->dropColumn('contact_information');
            $table->dropColumn('emergency_contact_1');
            $table->dropColumn('emergency_contact_2');
            $table->dropColumn('user_customer_id');
            $table->dropColumn('user_general_purpose_id');
            $table->dropIndex('email');
        });

        Schema::table('user_customers', function (Blueprint $table) {
            $table->dropIndex('email');
        });
    }
};