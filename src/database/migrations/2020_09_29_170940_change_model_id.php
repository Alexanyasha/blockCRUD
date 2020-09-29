<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeModelId extends Migration
{
    public function up()
    {
        Schema::table('block_items', function (Blueprint $table) {
            $table->string('model_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('block_items', function (Blueprint $table) {
            $table->bigInteger('model_id')->nullable()->change();
        });
    }
}
