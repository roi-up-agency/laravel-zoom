<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZoomEventsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_events_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('event')->index();
            $table->string('object_id')->index();
            $table->string('host_id')->index();
            $table->string('operator')->nullable();
            $table->text('object_data');
            $table->text('payload');
            $table->text('error_trace')->nullable();
            $table->enum('status', ['logging', 'logged', 'failed']);
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
        Schema::dropIfExists('zoom_events_log');
    }
}
