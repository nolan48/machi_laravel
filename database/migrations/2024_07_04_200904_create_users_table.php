<?php

// database/migrations/xxxx_xx_xx_create_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('user_name')->nullable();
            $table->string('user_nickname')->nullable();
            $table->string('user_account')->nullable();
            $table->string('user_password');
            $table->string('user_email')->unique();
            $table->string('user_gender')->default('不願透漏');
            $table->date('user_birthday')->nullable();
            $table->string('user_image')->nullable();
            $table->string('user_phone')->nullable();
            $table->string('user_address')->nullable();
            $table->string('user_notes')->nullable();
            $table->tinyInteger('user_status')->default(1);
            $table->string('google_uid')->nullable();
            $table->string('line_uid')->nullable();
            $table->text('line_access_token')->nullable();
            $table->timestamp('user_createtime')->useCurrent();
            $table->timestamp('user_updatetime')->useCurrent()->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};

