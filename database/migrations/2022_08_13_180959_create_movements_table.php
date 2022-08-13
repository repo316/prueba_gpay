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
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fk_id_users')->index('idx_fk_id_users');
            $table->bigInteger('fk_id_wallets')->index('idx_fk_id_wallets');
            $table->string('code',100)->index('idx_code');
            $table->enum('type', ['inbound','outbound'])->index('idx_type');
            $table->double('amount',18,5);
            $table->string('description',255)->nullable(true);
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
        Schema::dropIfExists('movements');
    }
};
