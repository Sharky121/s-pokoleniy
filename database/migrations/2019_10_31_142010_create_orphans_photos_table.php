<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrphansPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orphans_photos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('path');
            $table->integer('position');
            $table->unsignedBigInteger('orphan_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index('orphan_id');
            $table->foreign('orphan_id')->references('id')->on('orphans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orphans_photos');
    }
}
