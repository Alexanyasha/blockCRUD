<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHtmlContent extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('block_items', 'html_content')) {
            Schema::table('block_items', function (Blueprint $table) {
                $table->json('html_content')->after('content')->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('block_items', 'html_content')) {
            Schema::table('block_items', function (Blueprint $table) {
                $table->dropColumn('html_content');
            });
        }
    }
}
