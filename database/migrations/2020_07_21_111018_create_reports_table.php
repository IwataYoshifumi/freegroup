<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            
            $table->foreignId( 'user_id' );
            $table->foreignId( 'schedule_id' )->nullable();
            
            $table->string('name')->nullable();
            $table->string('place')->nullable();
            $table->dateTimeTz( 'start_time' );
            $table->dateTimeTz( 'end_time' )->nullable();
            $table->longtext( 'memo' )->nullable();
            
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
