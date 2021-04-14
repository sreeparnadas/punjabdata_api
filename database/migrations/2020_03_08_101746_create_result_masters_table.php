<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateResultMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('result_masters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('single_result')->nullable(true);
            $table->integer('jumble_number')->nullable(true);
            $table->date('game_date');

            $table->bigInteger('draw_details_id')->unsigned();
            $table ->foreign('draw_details_id')->references('id')->on('draw_details');

            $table->bigInteger('game_id')->unsigned()->nullable(true);
            $table ->foreign('game_id')->references('id')->on('games');

//            $table->unique(['game_date', 'draw_master_id']);

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
        Schema::dropIfExists('result_masters');
    }
}
