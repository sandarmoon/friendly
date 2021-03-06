<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarCityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_city', function (Blueprint $table) {
             $table->id();
            $table->BigInteger('car_id')->unsigned();   
            $table->BigInteger('city_id')->unsigned();
             $table->foreign('car_id')
                    ->references('id')
                    ->on('cars')
                    ->onDelete('cascade');
             $table->foreign('city_id')
                    ->references('id')
                    ->on('cities')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('car_city');
    }
}
