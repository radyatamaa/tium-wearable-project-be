<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesSmartWatchPublicWelfaresTable extends Migration

{
    public function up()
    {
        Schema::create('devices_smart_watch_public_welfares', function (Blueprint $table) {
            $table->id();
            $table->string('serial')->nullable();
            $table->string('name')->nullable();
            $table->string('registered_to')->nullable();
            $table->boolean('usage_status');
            $table->date('registration_date')->nullable();
            $table->unsignedBigInteger('user_general_purpose_id')->nullable();
            $table->unsignedBigInteger('user_customer_id')->nullable();
            $table->timestamps();

            $table->foreign('user_general_purpose_id', 'dswp_ugp_id_fk')
                  ->references('id')->on('user_general_purposes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('devices_smart_watch_public_welfares');
    }
}