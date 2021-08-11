<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string( 'type' )->default('public');               // private, camparny-wide

            $table->string('category');
            $table->string('sub_category'     )->nullable();
            
            $table->string('background_color' )->nullable();
            $table->string('text_color'       )->nullable();

            $table->string('control_number'   )->nullable();
            $table->string('location'         )->nullable();
            
            $table->longtext('memo')->nullable();

            $table->boolean( 'disabled' )->default( false );

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facilities');
    }
}
