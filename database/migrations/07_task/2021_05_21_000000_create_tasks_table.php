<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            $table->foreignId( 'user_id' );   // creator
            $table->foreignId( 'updator_id'); // updator
            $table->foreignId( 'user_who_complete')->nullable(); // 完了させたユーザ
            
            $table->foreignId( 'tasklist_id' );
            
            $table->string('name');
            $table->longtext( 'memo' )->nullable();

            $table->date( 'due_date' );
            $table->dateTimeTz( 'due_time' )->nullable();
            $table->boolean( 'all_day' )->nullable();
            
            $table->dateTimeTz( 'completed_time' )->nullable();

            $table->string( 'status' ); // 未完・完了・非表示
            
            $table->string( 'permission' ); // creator, attendees, writer
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
