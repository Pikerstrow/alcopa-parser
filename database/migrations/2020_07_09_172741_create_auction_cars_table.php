<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuctionCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auction_cars', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('car_make_id')->unsigned()->index();
            $table->bigInteger('car_model_id')->unsigned()->index();
            $table->bigInteger('auction_id')->unsigned()->index();
            $table->string('category')->index();
            $table->bigInteger('lot_number')->unsigned()->index();
            $table->bigInteger('lot_price')->unsigned()->index();
            $table->string('finish')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('fuel_type')->nullable();
            $table->date('first_produced')->nullable();
            $table->string('mileage')->nullable();
            $table->string('vin')->nullable();
            $table->string('storage_location')->nullable();
            $table->string('gearbox')->nullable();
            $table->text('description')->nullable();
            $table->text('inspection_report_url')->nullable();
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
        Schema::dropIfExists('auction_cars');
    }
}
