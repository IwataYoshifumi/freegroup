<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignID('user_id');               // 申請者ID
            $table->foreignID('allocated_vacation_id')->nullable();  // 消化する有給休暇割当のvacation_id　

            $table->string('status');                   // 承認待ち、承認、却下、取り下げ、休暇取得完了
            $table->string('type');                     // 特別休暇・有給休暇

            $table->date('date');                       // 申請日
            $table->date('start_date');                 // 休暇開始日
            $table->date('end_date');                   // 休暇終了日
            // $table->integer('start_time')->nullable();  //　休暇時間
            // $table->integer('end_time')->nullable();
            $table->time('start_time')->nullable();  //　休暇時間
            $table->time('end_time')->nullable(); ;
            $table->float('num', 100 ,5 );              // 休暇取得日数・時間（1時間なら 0.125）
            $table->date('approval_date')->nullable();  //承認日

            $table->string('reason');                   // 休暇理由
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
        Schema::dropIfExists('applications');
    }
}
