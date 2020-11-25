<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\myHttp\GroupWare\Models\Dept;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        
        DB::delete( 'delete from users' );
        
        $faker = Faker::create( 'ja_JP' );
        
        $grades = [ '部長', '課長', '主任', '' ];
        $depts = Dept::pluck('id')->all();
        $password = Hash::make( 'password' );
        
        $users = [
            [ 'name' => '岩田',
              'email' => 'iwata@network-tokai.jp',
              'email_verified_at' => now(),
              'password' => $password,
              'dept_id'  => $faker->randomElement( $depts ),
              'grade'   => 'テストユーザ'
            ],
            [ 'name' => '好史',
              'email' => 'yoshifumi@iwatan.com',
              'email_verified_at' => now(),
              'password' => $password,
              'dept_id'  => $faker->randomElement( $depts ),
              'grade'   => 'テストユーザ'
            ],
        ];
        
        $num = 20;
        for( $i = 0; $i <= $num; $i++ ) {
            $user = [ 'name' =>  $faker->name,
                      'email' => $faker->unique()->safeEmail,
                       'email_verified_at' => now(),
                      'password' => $password,
                      'dept_id'  => $faker->randomElement( $depts ),
                      'grade'   => $grades[ rand( 0,3 ) ],
                      ];
            array_push( $users, $user );
        }

        DB::table( 'users' )->insert( $users );

    }
}
