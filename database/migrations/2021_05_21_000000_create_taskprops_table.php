<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskPropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_props', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            $table->foreignId( 'user_id' );
            $table->foreignId( 'task_list_id' );

            $table->string('name');
            $table->string('memo')->nullable();
            $table->string( 'background_color' )->nullable();
            $table->string( 'text_color' )->nullable();

            $table->boolean( 'not_use' )->default( false );
            $table->boolean( 'hide' )->default( false );
            $table->string( 'default_permission' )->default('creator');  // creator, attendees, writers, 
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_props');
    }
}
