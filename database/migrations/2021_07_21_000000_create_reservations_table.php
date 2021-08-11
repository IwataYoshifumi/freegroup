<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            $table->foreignId( 'user_id' );   // creator
            $table->foreignId( 'facility_id' );  

            $table->text( 'purpose' )->nullable();      
            $table->longtext( 'memo' )->nullable();

            // $table->date( 'start_date' );
            // $table->date( 'end_date' );
            $table->dateTimeTz( 'start' );
            $table->dateTimeTz( 'end'   );
            $table->boolean( 'all_day' )->nullable();

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
