<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessListablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accesslistables', function (Blueprint $table) {
            $table->foreignId( 'access_list_id' );
            $table->foreignId( 'accesslistable_id' );
            $table->string( 'accesslistable_type' );
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accesslistables');
    }
}
