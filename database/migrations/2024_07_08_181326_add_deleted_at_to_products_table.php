<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->softDeletes(); // 添加 deleted_at 列
        });
    }

    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropSoftDeletes(); // 删除 deleted_at 列
        });
    }
}
