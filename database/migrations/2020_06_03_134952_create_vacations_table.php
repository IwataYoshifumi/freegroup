<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVacationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacations', function (Blueprint $table) {
            $table->id();

            $table->foreignID('user_id');
            $table->foreignID('application_id')->nullable();
            $table->string( 'action');
            $table->string( 'type' )->nullable();
            $table->integer( 'year' )->nullable();
            $table->date( 'allocate_date')->nullable();
            $table->date( 'expire_date' )->nullable();
            $table->boolean( 'done_expired' )->default( false );
            $table->float( 'num' ,100,5)->nullable();
            $table->float( 'allocated_num'  ,100,5 )->nullable();
            $table->float( 'remains_num'    ,100,5 )->nullable();
            $table->float( 'application_num',100,5 )->nullable();
            $table->float( 'approval_num'   ,100,5 )->nullable();
            $table->float( 'completed_num'  ,100,5 )->nullable();
            $table->float( 'expired_num'    ,100,5 )->nullable();
            
            $table->timestamps();
            // $table->unique( ['user_id', 'year'] ); // 有給休暇の割り当ては年に１回のみ
            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vacations');
    }
}
