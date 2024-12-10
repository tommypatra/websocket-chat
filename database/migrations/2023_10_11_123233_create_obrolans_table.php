<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObrolansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('obrolans', function (Blueprint $table) {
            $table->id();
            $table->text('pesan');
            $table->boolean('is_baca')->default(false);
            $table->timestamps();

            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('ruang_obrolan_id');
            $table->foreign('ruang_obrolan_id')->references('id')->on('ruang_obrolans')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('obrolans');
    }
}
