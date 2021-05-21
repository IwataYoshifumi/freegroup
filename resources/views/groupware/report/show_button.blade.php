@php

$report_id = [ 'report' => $report->id ];


$route_edit       = route( 'groupware.report.edit'  , $report_id ); 
$route_copy       = route( 'groupware.report.copy'  , $report_id ); 
$route_delete     = route( 'groupware.report.delete', $report_id ); 
$route_new_report = route( 'groupware.report.create'  , $report_id ); 
$show_more_info   = ( $route_name == 'groupware.report.show' ) ? 1 : 0;

@endphp

<div class="row m-1 w-100 container">

    @can( 'update', $report )
        <a class="btn icon_btn uitooltip" style="font-size: 20px;" href="{{ $route_edit }}" title="変更">
            @icon( edit )
        </a>
    @endcan
    
    <a class="btn col-1 m-1 uitooltip" style="font-size: 20px;" href="{{ $route_copy }}" title="複製">
        <i class="fas fa-copy"></i>
    </a>
    
        
    @can( 'delete', $report )
        <a class="btn btn_icon uitooltip" style="font-size: 20px;" href="{{ $route_delete }}" title="削除">
            @icon( trash-alt )
        </a>
    @endcan
    
    @if( $show_more_info )
        <div class="ml-auto">
            @if( $report_list->is_disabled() )
                 <span class="alert-danger p-2 uitooltip" title="日報リストが無効化中の為、編集・削除はできません">日報リスト管理者設定により無効化中</span>   
            @else
                @if( $report->user_id == $auth->id )
                    @if( $report->permission == "attendees" )
                        関連社員も予定修正可
                    @elseif( $report->permission == "writers" )
                        カレンダー編集者も予定修正可
                    @endif
                @endif
            @endif
            <button class="btn m-1" id="report_info_btn"><i class="fas fa-info-circle" style="font-size: 21px; color: black;"></i></button>
        </div>
    @endif
</div>

@if( $show_more_info )
    <div id="report_info_dialog" title="その他の情報">
    
        <div class="row">
            <div class="col-md-4 text-md-right">作成者</div>
            <div class="col-md-8">
                {{ $creator->p_dept_name() }} {{ $creator->name }} {{ $creator->grade }}
            </div>

            @if( $report->user_id != $report->updator_id )
                <div class="col-md-4 text-md-right">更新者</div>
                <div class="col-md-8">
                    {{ $updator->p_dept_name() }} {{ $updator->name }} {{ $updator->grade }} 更新
                </div>
            @endif

            <div class="col-md-4 text-md-right">作成日時</div>
            <div class="col-md-8">
                {{ $report->created_at }}
            </div>
            @if( $report->updated_at != $report->created_at )
                <div class="col-md-4 text-md-right">更新日時</div>
                <div class="col-md-8">
                    {{ $report->updated_at }}
                </div>
            @endif
    
            <div class="col-md-4 text-md-right">スケジュール変更権限</div>
            <div class="col-md-8">
                @if( $report->permission == "creator" )
                    予定作成者のみ変更可能
                @elseif( $report->permission == "attendees" )
                    関連社員も予定変更可能
                @elseif( $report->permission == "writers" )
                    関連社員・カレンダー編集者も予定変更可能
                @endif
                {{ $report->permission }}
            </div>

            <div class="col-md-4 text-md-right">閲覧範囲</div>
            <div class="col-md-8">
                @if( $report_list->type == 'public' )
                    公開
                @elseif( $report_list->type == 'private' )
                    閲覧制限あり
                @elseif( $report_list->type == 'campany-wide' )
                    全社公開
                @endif
                {{ $report_list->type }}
            </div>
            
                        
        
            
        </div>
    </div>
    
    <script>
        $( function() {
            $('#report_info_dialog').dialog({
                autoOpen: false,
                height: 'auto',
                width: 600,
                show: { effect: 'blind',   duration: 10 },
                hide: { effect: 'explode', duration: 10 }
            }); 
            $('#report_info_btn').on( 'click', function() {
                $('#report_info_dialog').dialog('open'); 
            });
        });    
        
        
    </script>
@endif
    