<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMiddleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //订单与商品中间表
        Schema::create('order_dishes', function (Blueprint $table) {
            $table->engine ='InnoDB';
            $table->integer('order_id');
            $table->integer('goods_id');
            $table->increments('id');
            $table->string('good_name');
            $table->string('good_image');
            $table->string('good_price');
            $table->integer('amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
