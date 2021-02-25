<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGCalSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gcal_syncs', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId( 'schedule_id' );
            $table->foreignId( 'calprop_id' );
            $table->unique( ['schedule_id', 'calprop_id'] );

            $table->string(  'google_event_id'  );
            $table->string(  'google_etag'      )->nullable();
            $table->dateTimeTZ('google_synced_at' );

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
        Schema::dropIfExists('gcal_syncs');
    }
}
