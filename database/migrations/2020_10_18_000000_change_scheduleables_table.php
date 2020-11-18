<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeScheduleablesTable extends Migration
{
    public function up()
    {
        Schema::table('scheduleables', function (Blueprint $table) {
            $table->string( 'google_calendar_event_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table( 'scheduleables', function( Blueprint $table ) {
            $table->dropColumn( 'google_calendar_event_id');
        });
    }
}