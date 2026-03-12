<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVeteransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('veterans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date');
            $table->text('title');
            $table->text('content_short')->nullable();
            $table->text('content_long')->nullable();
            $table->text('place')->nullable();
            $table->text('cover');
            $table->unsignedSmallInteger('is_active');

            $table->text('url')->nullable();
            $table->text('title_seo')->nullable();
            $table->text('description_seo')->nullable();
            $table->text('keywords_seo')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('veterans');
    }
}
