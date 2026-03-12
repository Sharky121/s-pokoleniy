<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages_galleries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('key');
            $table->integer('position')->nullable();
            $table->unsignedBigInteger('page_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index('page_id');
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages_galleries');
    }
}
