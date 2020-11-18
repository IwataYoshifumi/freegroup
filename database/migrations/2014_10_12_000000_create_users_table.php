<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('retired')->default(false);
            $table->date('date_of_retired')->nullable();

            // 下記、有給休暇申請システム用カラム
            //
            $table->string( 'code' )->unique()->nullalbe(); //社員ID
            $table->foreignID('dept_id');
            $table->string('grade')->nullable();
            $table->date('join_date')->nullable(); // 入社年月日
            $table->string('carrier')->nullable(); // 中途・新卒
            $table->string('memo')->nullable();
            $table->text('browsing');               // 閲覧権限
            $table->integer('apploval_master_id')->nullable(); // 申請マスター
            //
            // 上記まで有給休暇申請システム用カラム
            
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
