@php
if( is_debug() ) {
    $route = [  
    
        //
        //　開発用ルート
        //
        99 => route( 'groupware.test.files'),
        98 => route( 'groupware.file.deleteAllUntachedFiles'),
    
        // スクリーンサイズ系
        //
        97 => route( 'screensize.get' ),
        96 => route( 'screensize.dump' ),
        95 => route( 'screensize.forget' ),
        
        // 複数ファイル削除
        94 => route( 'groupware.test.delete_files'),
        
        //　日報リスト開発用ルート
        //
        100 => route( 'groupware.test.search_report_lists' ),
        
        
        // テスト
        //
        1000   => route( 'groupware.test.template'),
        1001   => route( 'groupware.test.test'),
        1002   => route( 'groupware.test.custome_blade_icons'),
        1003   => route( 'groupware.test.depts_users_customers'),
        ];
}
@endphp


@if( is_debug() and auth() )

    <div class="dropdown">
        <a class="nav-item nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">開発用</a>
        <ul class="menu dropdown-menu">
            <li><div>Test</div>
                <ul>
                    <li><a class="dropdown-item" href="{{ $route[1000] }}">Test Template</a></li>
                    <li><a class="dropdown-item" href="{{ $route[1001] }}">Test</a></li>
                    <li><a class="dropdown-item" href="{{ $route[1002] }}">Blade Icons</a></li>
                    <li><a class="dropdown-item" href="{{ $route[1003] }}">Depts Users Customers</a></li>
                    <li><a class="dropdown-item" href="{{ $route[100]  }}">Search ReportList</a></li>
                    
                </ul>
            </li>
            <li><div>File</div>
                <ul>
                    <li><a class="dropdown-item" href="{{ $route[94] }}">複数ファイル削除</a></li>
                    <li><a class="dropdown-item" href="{{ $route[98] }}">無添付全ファイル一括削除</a></li>
                    <li><a class="dropdown-item" href="{{ $route[99] }}">ファイル入力コンポーネント</a></li>
                </ul>
            </li>
            <li><div>ScreenSize</div>
                <ul>     
                    <li><a class="nav-item nav-link" href="{{ $route[97] }}">set</a></li>
                    <li><a class="nav-item nav-link" href="{{ $route[96] }}">dump</a></li>
                    <li><a class="nav-item nav-link" href="{{ $route[95] }}">forget</a></li>
                </ul>
            </li>
        </ul>
    </div>
@endif