<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('document')->unique();
            $table->string('name');
            $table->string('email')->index('idx_email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone',100)->index('idx_phone');
            $table->string('password');
            $table->string('token')->index('idx_token');
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
        Schema::dropIfExists('users');
    }
};
