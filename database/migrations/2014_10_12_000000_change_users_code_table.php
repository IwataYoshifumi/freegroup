<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// class CreateUsersTable extends Migration
class ChangeUsersCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('users', function (Blueprint $table) {
        Schema::table('users', function (Blueprint $table) {
            // $table->string( 'code' )->nullalbe(true)->change(); //社員ID
            $table->dropColumn('code');
            $table->dropColumn('browsing');
            $table->dropColumn('admin');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->string( 'code' )->unique()->nullalble();
            $table->text('browsing')->nullable();

        });

    }
    
}
