<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublishingsPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publishings_photos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('path');
            $table->integer('position');
            $table->unsignedBigInteger('publishing_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index('publishing_id');
            $table->foreign('publishing_id')->references('id')->on('publishings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publishings_photos');
    }
}
