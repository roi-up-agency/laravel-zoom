<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZoomMailsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_mails_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('registrant_id')->index();
            $table->string('meeting_id')->index();
            $table->string('occurrence_id')->index();
            $table->string('action')->index();
            $table->string('subject');
            $table->string('sendee')->index();
            $table->text('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zoom_mails_log');
    }
}
