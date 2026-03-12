<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesGalleriesPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages_galleries_photos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('path');
            $table->integer('position')->nullable();
            $table->unsignedBigInteger('page_gallery_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index('page_gallery_id');
            $table->foreign('page_gallery_id')->references('id')->on('pages_galleries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages_galleries_photos');
    }
}
