<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarVersionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_version_items', function (Blueprint $table) {
            $table->id();
            $table->string('price');
            $table->string('width');
            $table->string('height');
            $table->string('traction');
            $table->string('fuel');
            $table->string('cc');
            $table->string('doors');
            $table->string('air_bag');
            $table->string('abs');
            $table->string('steering_wheel');
            $table->string('air_cond');
            $table->string('bluetooth');
            $table->string('screen');
            $table->string('android');
            $table->string('tires');
            $table->bigInteger('car_version_id');
            $table->string('url_reference');
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
        Schema::dropIfExists('car_version_items');
    }
}
