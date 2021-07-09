<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;
use App\myHttp\GroupWare\Models\Task;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the admin is authorized to make this request.
     *
     * @return bool
     */
    public $start; // dateTime
    public $end;   // dateTime 
    
    public function authorize() {
        return true;
    }
    
    // protected function prepareForValidation() {
    // }

    public function rules() {

        $user     = $this->user();
        $route_name = Route::currentRouteName();
        
        if( $route_name == 'groupware.task.update') {
            $task = $this->route( 'task' ); 
    
            if( $task->creator->id == $user->id ) {
                $rules['permission']  = 'required';
            }
        }

        $rules['name']     = 'required';
        $rules['due_date'] = 'required|date';
        if( empty( $this->all_day )) {
            $rules['due_time'] = 'required|regex:/^\d{1,2}:\d{1,2}$/';
        }
        $rules['tasklist_id'] = 'required|integer';
        
        //　有効なタスクリストか確認
        //
        if( $this->tasklist_id ) {
            
            $invalid = TaskList::where( 'id', $this->tasklist_id )->where( function( $query ) use ( $route_name ) {
                    if( $route_name == "groupware.task.create" ) {
                        $query->where( 'not_use', 1 )->orWhere( 'disabled', 1 );
                    } else {
                        $query->orWhere( 'disabled', 1 );
                    }
                })->get();
                
            // dd( $num );
            if( count( $invalid ) >= 1 ) {
                $rules['tasklist_invalid'] = 'required';
            }
        }
        // dd( $this, $this->all_day, $this->start_date, $rules );
        
        return $rules;
    }
    
    public function messages() {
        $messages = [
                'name.required'         => 'タスク名を入力してください。',
                'due_date.required'   => '期日を入力してください',
                'due_date.date'       => '期日がが不正です',
                'due_time.required'   => '期限時刻を入力してください',
                'due_time.regex'      => '期限時刻が不正です。',

                'tasklist_id.required' => 'タスクリストを選択してください',
                'tasklist_invalid.required' => 'タスクリストが不正です',
            
            ];        

        return $messages;
        
    }
    
}
