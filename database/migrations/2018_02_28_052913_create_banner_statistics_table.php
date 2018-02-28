<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('all')->unsigned();
            $table->integer('unique')->unsigned();

            $table->integer('banner_id')->unsigned();
            $table->foreign('banner_id')
                ->references('id')->on('banners')
                ->onDelete('cascade');

            $table->timestamp('date');
            $table->index('date');
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
        Schema::dropIfExists('banner_statistics');
    }
}
