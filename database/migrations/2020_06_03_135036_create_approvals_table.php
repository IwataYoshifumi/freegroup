<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignID('application_id');    // 申請ID
            // $table->foreignID('user_id');           // 承認者ID
            $table->foreignID('applicant_id');           // 申請者ID
            $table->foreignID('approver_id');           // 承認者ID
            $table->dateTime('date')->nullable();   // 承認日時
            $table->string('status');               // ステータス
            $table->string('comment')->nullable();
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
        Schema::dropIfExists('approvals');
    }
}
