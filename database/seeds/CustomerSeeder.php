<?php

use Illuminate\Database\Seeder;

#/baseDB/database/factories/CustomerFactory.php

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
     
    public function run() {

        factory( App\Models\Customer::class, 50 )->create();

        //     DB::table('customers')->insert([
        //         'name' => Str::random(10),
        //         'kana' => Str::random(10),
        //         'email' => Str::random(10).'@gmail.com',
        //         'password' => Hash::make('password'),
        //     ]);
    }

}
