<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVeteransPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('veterans_photos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('path');
            $table->integer('position');
            $table->unsignedBigInteger('veteran_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index('veteran_id');
            $table->foreign('veteran_id')->references('id')->on('veterans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('veterans_photos');
    }
}
