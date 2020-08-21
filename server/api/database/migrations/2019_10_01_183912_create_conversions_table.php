<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('click_id')->nullable();
            $table->string('payout', 50);
            $table->string('currency', 50);
            $table->string('ex1', 150)->nullable();
            $table->string('ex2', 150)->nullable();
            $table->string('ex3', 150)->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';
            
            
        });

        Schema::table('conversions', function (Blueprint $table) {
            $table->foreign('click_id')->references('id')->on('clicks');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversion');
        Schema::dropIfExists('conversions');
    }
}
