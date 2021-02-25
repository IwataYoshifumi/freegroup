<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalPropTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calprops', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            $table->foreignId( 'user_id' );
            $table->foreignId( 'calendar_id' );

            $table->string('name');
            $table->string('memo')->nullable();
            $table->string( 'background_color' )->nullable();
            $table->string( 'text_color' )->nullable();

            $table->boolean( 'not_use' )->default( false );
            $table->boolean( 'hide' )->default( false );
            $table->string( 'default_permission' )->default('creator');  // creator, attendees, writers, 
            
            $table->boolean( 'google_sync_on' )->default( false );
            $table->boolean( 'google_sync_check' )->default( false );
            $table->boolean( 'google_sync_bidirectional' )->default( false );
            
            $table->integer( 'google_sync_span' )->nullable();
            $table->string( 'google_sync_level' )->nullable();
            $table->string(  'google_calendar_id')->nullable();
            $table->string(  'google_id')->nullable();
            // $table->dateTime( 'google_updated_at' )->nullable();
            $table->dateTime( 'google_synced_at' )->nullable();
            $table->foreignId( 'google_private_key_file_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calprops');
    }
}
