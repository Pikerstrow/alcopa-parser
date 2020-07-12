<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableParserInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parser_info', function (Blueprint $table) {
            $table->id();
            $table->timestamp('last_parsing_date')->nullable();
            $table->tinyInteger('last_parsing_result')->unsigned()->default(1);
            $table->text('parsed_auctions')->nullable();
            $table->text('parsed_cars')->nullable();
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
        Schema::dropIfExists('table_parser_info');
    }
}
