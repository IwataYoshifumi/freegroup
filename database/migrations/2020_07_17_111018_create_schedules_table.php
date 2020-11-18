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
            
            $table->foreignId( 'user_id' );
            
            $table->string('name');
            $table->string('place')->nullable();
            $table->dateTimeTz( 'start_time' );
            $table->dateTimeTz( 'end_time' )->nullable();
            $table->string( 'period' )->nullable();
            $table->string( 'notice' )->nullable();
            $table->longtext( 'memo' )->nullable();
            
            $table->foreignId( 'schedule_type_id' );
            $table->string( 'google_calendar_event_id')->nullable();
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
