<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePlayDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('play_details', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('play_master_id')->unsigned();
            $table ->foreign('play_master_id')->references('id')->on('play_masters');

            $table->bigInteger('play_series_id')->unsigned();
            $table ->foreign('play_series_id')->references('id')->on('play_series');

            $table->integer('row_num');
            $table->integer('col_num');
            $table->integer('val_one')->default(0);
            $table->integer('val_two')->default(0);
            $table->integer('game_value');

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('play_details');
    }
}
