<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        
        DB::delete( 'delete from admins' );
        
        $password = Hash::make( 'password' );
        
        $admins = [
            [ 'name' => '管理者　岩田',
              'email' => 'iwata@network-tokai.jp',
              'email_verified_at' => now(),
              'password' => $password,
            ],
            [ 'name' => '管理者　好史',
              'email' => 'yoshifumi@iwatan.com',
              'email_verified_at' => now(),
              'password' => $password,
            ],
        ];
        
        $faker = Faker::create();
        
        $num = 5;
        for( $i = 0; $i <= $num; $i++ ) {
            $admin = [ 'name' =>  $faker->name,
                      'email' => $faker->unique()->safeEmail,
                       'email_verified_at' => now(),
                      'password' => $password,
                      ];
            array_push( $admins, $admin );
        }

        DB::table( 'admins' )->insert( $admins );
        
    }
}
