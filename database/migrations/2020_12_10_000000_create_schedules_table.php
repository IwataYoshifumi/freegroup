<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            $table->foreignId( 'user_id' );   // creator
            $table->foreignId( 'updator_id'); // updator
            $table->foreignId( 'calendar_id' );
            
            $table->string('name');
            $table->string('place')->nullable();
            $table->longtext( 'memo' )->nullable();

            $table->date( 'start_date' );
            $table->date( 'end_date' );
            $table->dateTimeTz( 'start' )->nullable();
            $table->dateTimeTz( 'end'   )->nullable();
            $table->boolean( 'all_day' )->nullable();

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
        Schema::dropIfExists('schedules');
    }
}
