<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_page_id')->nullable();
            $table->text('view')->nullable();
            $table->text('menu')->nullable();
            $table->integer('position')->nullable();
            $table->text('class')->nullable();
            $table->text('title');

            // Или так, или массив. У каждого варианта есть преимущества и недостатки.
            $table->longText('content_0')->nullable();
            $table->longText('content_1')->nullable();
            $table->longText('content_2')->nullable();
            $table->longText('content_3')->nullable();
            $table->longText('content_4')->nullable();
            $table->longText('content_5')->nullable();
            $table->longText('content_6')->nullable();
            $table->longText('content_7')->nullable();
            $table->longText('content_8')->nullable();
            $table->longText('content_9')->nullable();

            $table->text('url')->nullable();
            $table->text('title_seo')->nullable();
            $table->text('description_seo')->nullable();
            $table->text('keywords_seo')->nullable();

            $table->unsignedSmallInteger('is_active');
            $table->timestamps();
            $table->softDeletesTz();

            $table->foreign('parent_page_id')->references('id')->on('pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
