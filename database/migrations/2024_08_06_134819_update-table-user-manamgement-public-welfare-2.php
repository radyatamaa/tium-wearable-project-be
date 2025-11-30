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
        Schema::table('user_management_public_welfares', function (Blueprint $table) {
            $table->string('affiliations')->nullable()->change();
            $table->unsignedBigInteger('user_customer_id')->nullable();
            $table->foreign('user_customer_id', 'um_pw_cust_id_fk')
                ->references('id')->on('user_customers')->onDelete('cascade');

            $table->index('email');

        });
    }
};