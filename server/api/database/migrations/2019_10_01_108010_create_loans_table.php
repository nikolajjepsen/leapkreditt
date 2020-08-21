<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('url', 125);
            $table->unsignedBigInteger('country_id')
                    ->nullable();
            $table->integer('active')->default(0);
            $table->integer('min_amount');
            $table->integer('max_amount');
            $table->float('yearly_cost_percent');
            $table->integer('min_age');
            $table->integer('min_tenure');
            $table->integer('max_tenure');

            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
