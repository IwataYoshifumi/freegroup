<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleGroupablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rolegroupables', function (Blueprint $table) {

            $table->foreignId( 'role_group_id' );
            $table->foreignId( 'rolegroupable_id' );
            $table->string( 'rolegroupable_type' );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rolegroupables');
    }
}
