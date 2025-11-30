<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserManagementReportPublicWelfaresTable extends Migration

{
    public function up()
    {
        Schema::create('user_management_report_public_welfares', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gender');
            $table->integer('age');
            $table->string('affiliations');
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->timestamps();

            $table->foreign('user_general_purpose_id', 'umrpw_ugp_id_fk')
                  ->references('id')->on('user_general_purposes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_management_report_public_welfares');
    }
}