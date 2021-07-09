<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_lists', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('memo')->nullable();

            $table->string( 'type' )->default('public');               // public , private, camparny-wide
            $table->string( 'default_permission' )->default('creator');// creator, attendees, writers, 
            $table->boolean( 'not_use' )->default( false );
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
        Schema::dropIfExists('task_lists');
    }
}
