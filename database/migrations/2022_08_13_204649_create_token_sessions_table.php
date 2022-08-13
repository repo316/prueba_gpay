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
        Schema::create('token_sessions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fk_id_users')->index('idx_fk_id_users');
            $table->bigInteger('fk_id_payment')->index('fk_id_payment');
            $table->string('session')->index('idx_session');
            $table->string('token')->index('idx_token');
            $table->dateTime('date_end')->index('idx_date_end');
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
        Schema::dropIfExists('token_sessions');
    }
};
