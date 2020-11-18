<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// class CreateUsersTable extends Migration
class ChangeSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('users', function (Blueprint $table) {
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('schedule_type_id');
            $table->string( 'google_calendar_event_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('schedule_type_id');
            $table->dropColumn( 'google_calendar_event_id');
        });

    }
    
}
