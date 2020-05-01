<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZoomModelsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zoom_models_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_log_id')->index()->nullable();
            $table->string('model')->index();
            $table->integer('model_id')->index();
            $table->string('action')->index();
            $table->text('attributes');
            $table->text('changes')->nullable();
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
        Schema::dropIfExists('zoom_models_log');
    }
}
