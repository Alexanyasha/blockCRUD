<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMenuItemsTable extends Migration
{
    public function up()
    {
        Schema::create('block_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('type', 20)->default('html');
            $table->text('content')->nullable();
            $table->string('model', 255)->nullable();
            $table->bigInteger('model_id')->unsigned()->nullable();
            $table->tinyInteger('publish')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('block_items');
    }
}
