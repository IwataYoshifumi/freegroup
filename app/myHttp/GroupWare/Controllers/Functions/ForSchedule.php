<?php
namespace App\myHttp\GroupWare\Controllers\Functions\ForSchedule;

    function test_function() {
        dump( __FILE__);
    }

// //　カレンダー月表示用の日付データの生成
// //

// function getMonthlyCalendarDates( Carbon $base_date ) {
   
//     $date = new Carbon( "{$base_date->year}-{$base_date->month}-01" );
    
//     // MEMO: 月末が日曜日の場合の挙動を修正
//     $addDay = ( $date->copy()->endOfMonth()->isSunday()) ? 7 : 0;
    
//     // カレンダーを四角形にするため、前月となる左上の隙間用のデータを入れるためずらす
//     $date->subDay( $date->dayOfWeek );

//     // 同上。右下の隙間のための計算。
//     // MEMO: 変数に修正
//     // $count = 31 + $date->dayOfWeek;
//     $count = 31 + $addDay + $date->dayOfWeek;
//     $count = ceil($count / 7) * 7;
//     $dates = [];

//     for ($i = 0; $i < $count; $i++, $date->addDay()) {
//         // copyしないと全部同じオブジェクトを入れてしまうことになる
//         $dates[] = $date->copy();
//     }
//     return $dates;
        
// }

// //　キーが日付、値がscheuled_idの配列を作る（カレンダー表示で使うためのデータ）
// //
// function get_array_dates_schedule_id( $schedules ) {
//         dump( $schedules );
//     $dates = [];
//     $i = 1;
//     foreach( $schedules as $schedule ) {
//         $start_date = new Carbon( $schedule->start_date );
//         $end_date   = new Carbon( $schedule->end_date   );
        
//         for( $date = $start_date->copy(); $date->lte( $end_date ); $date->addDay() ) {
            
//             $d = $date->format( 'Y-m-d' );
//             if( array_key_exists( $d, $dates )) {
//                 array_push( $dates[$d], $schedule->id );
//             } else {
//                 $dates[$d] = [ $schedule->id ];
//             }
//             // dump( 'ID:'.$schedule->id."  date:".$date->format( 'Y-m-d')."   start:".$start_date->format( 'Y-m-d')."   end_date:".$end_date->format( 'Y-m-d') );
//             if( $i >= 100 ) { break; }
//             $i++;
//         }
//         if( $i >= 100 ) { break; }

//     }
//     // dump( $dates );
//     return $dates;        
// }