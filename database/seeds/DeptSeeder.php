<?php


use Illuminate\Database\Seeder;

class DeptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        
        DB::delete( 'delete from depts' );
        $depts = [ 
            [ 'name' => '総務部' ],
            [ 'name' => '営業部' ],
            [ 'name' => '技術部' ],
            [ 'name' => '企画部' ],
            [ 'name' => '秘書室' ],
            [ 'name' => '東京支社' ],
            [ 'name' => '大阪支社' ],
            [ 'name' => '福岡支社' ],
            [ 'name' => '札幌支社' ],
            [ 'name' => '仙台支社' ],
            [ 'name' => 'シンガポール支社' ],
        ];
        
        DB::table( 'depts' )->insert( $depts );

            
            
    }
}
