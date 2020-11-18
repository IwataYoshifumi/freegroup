<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            $table->foreignId( 'user_id' );
            $table->string('name');
            $table->string( 'color' )->nullable();
            $table->string( 'text_color' )->nullable();
            $table->string( 'class' );

            // $table->foreignId( 'file_id' )->nullable();
            $table->string('google_calendar_id')->nullable();
            $table->string( 'google_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_types');
    }
}
