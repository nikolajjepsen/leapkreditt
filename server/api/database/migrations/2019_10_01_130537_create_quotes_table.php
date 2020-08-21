<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('site_id')->nullable();
            $table->string('firstname', 100);
            $table->string('lastname', 100);
            $table->string('email', 150)->unique();
            $table->string('mobile', 150)->unique();
            $table->integer('tenure')->nullable();
            $table->integer('age')->nullable();
            $table->integer('loan_amount');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->foreign('site_id')->references('id')->on('sites');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotes');
    }
}
