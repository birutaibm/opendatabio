<?php

/*
 * This file is part of the OpenDataBio app.
 * (c) OpenDataBio development team https://github.com/opendatabio
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBibReferencesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bib_references', function (Blueprint $table) {
            $table->increments('id');
            $table->text('bibtex');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('bib_references');
    }
}
