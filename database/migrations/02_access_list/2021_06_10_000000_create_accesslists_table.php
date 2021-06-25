<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('memo')->nullable();
            $table->timestamps();
            
            // 追加 2021.6.10
            //
            $table->integer('num_owners');
            $table->integer('num_writers');
            $table->integer('num_readers');
            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_lists');
    }
}
