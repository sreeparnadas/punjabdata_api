<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrawDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('draw_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('draw_master_id')->unsigned()->nullable(false);
            $table ->foreign('draw_master_id')->references('id')->on('draw_masters');

            $table->integer('game_id');
            $table->string('draw_name');

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
        Schema::dropIfExists('draw_details');
    }
}
