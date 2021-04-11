<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaxTerminalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('max_terminals', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('stockist_id')->unsigned();
            $table ->foreign('stockist_id')->references('id')->on('stockists');

            $table->bigInteger('current_value')->default(0);
            $table->smallInteger('financial_year');
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
        Schema::dropIfExists('max_terminals');
    }
}
