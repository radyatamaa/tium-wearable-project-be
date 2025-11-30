<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmergencyReportPublicWelfaresTable extends Migration

{
    public function up()
    {
        Schema::create('emergency_report_public_welfares', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_customer_id');
            $table->string('assortment');
            $table->timestamp('notification_time');
            $table->text('content');
            $table->timestamp('confirmation_time')->nullable();
            $table->timestamp('action_time')->nullable();
            $table->string('name');
            $table->string('main_symptom');
            $table->string('gender');
            $table->integer('age');
            $table->string('contact');
            $table->string('affiliations');
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->unsignedBigInteger('measure_id')->nullable();
            $table->timestamps();

            $table->foreign('user_general_purpose_id', 'erpw_ugp_id_fk')
                  ->references('id')->on('user_general_purposes')->onDelete('cascade');
            $table->foreign('user_customer_id', 'erpw_uc_id_fk')
                  ->references('id')->on('user_customers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('emergency_report_public_welfares');
    }
}