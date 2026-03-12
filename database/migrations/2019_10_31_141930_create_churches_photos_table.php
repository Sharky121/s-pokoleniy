<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChurchesPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('churches_photos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('path');
            $table->integer('position');
            $table->unsignedBigInteger('church_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index('church_id');
            $table->foreign('church_id')->references('id')->on('churches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('churches_photos');
    }
}
