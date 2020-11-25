<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            
            //$table->timestamp('email_verified_at')->nullable();
            //$table->string('password')->nullable();
            //$table->rememberToken();

            // customer 用カラム
            //
            $table->string('kana')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('prefecture')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('building')->nullable();
            $table->string('tel')->nullable();
            $table->string('fax')->nullable();
            $table->string('mobile')->nullable();
            $table->date('birth_day')->nullable();
            $table->string('sex')->nullable();
            $table->string('memo')->nullable();
            
            // セールスフォースＩＤ
            //
            // $table->string( 'salse_force_id')->nullable();
            $table->string( 'salseforce_id')->nullable();
            
            //  名刺管理Sansan連携
            //
            // $table->string( 'sansan_person_id' )->nullable();
            // $table->timestamp( 'synced_at' )->nullable();  // Sansanデータ同期日時
            
            $table->softDeletes();
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
        //　ソフトデリート
        //
        Schema::table('customers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::dropIfExists('customers');
        
    }
}
