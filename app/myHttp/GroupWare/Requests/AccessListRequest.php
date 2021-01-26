<?php

namespace App\myHttp\GroupWare\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Validation\Rule;

use Arr;

use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;


class AccessListRequest extends FormRequest
{
    /**
     * Determine if the admin is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    // public function validationData() {
    // }
    
    // protected function prepareForValidation() {
    // }

    public function rules() {
        // dd( $this->input() );

        $auth = auth( 'user' )->user();
        $rules['name'] = 'required';

        $orders = $this->orders;
        $roles  = $this->roles;
        $types  = $this->types;
        $depts  = $this->depts;
        $groups = $this->groups;
        $users  = $this->users;

        // if_debug( $orders, $roles, $types );
        
        $owners = [];
        foreach( $orders as $j => $i ) {
            // if_debug( "$j, $i, $roles[$i], $types[$i], $depts[$i], $groups[$i], $users[$i]", $rules);
            
            // 最初のロールは writer で入力されていなければならない。
            if( $j === 0 ) {
                if( $roles[$i] != "owner" or empty( $types[$i] )) {
                    $rules['first_owner'] = 'required';
                    break;
                } else {
                    if(( $types[$i] == "dept"  and empty( $depts[$i]  )) or
                       ( $types[$i] == "user"  and empty( $users[$i]  )) or
                       ( $types[$i] == "group" and empty( $groups[$i] ))) {
                            $rules['first_input'] = 'required';
                            break;
                    }
                    $pre_role = "owner";
                }
            } else {
                // ロールの順序チェック
                // owner は最初に固まっていなければならない
                if( empty( $roles[$i] )) { continue; }
                $role = $roles[$i];

                if( $pre_role == "owner" ) {
                    $pre_role = $role;
                } else {
                    if( $role == "owner" ) { $rules['role_order'] = 'required'; break; }
                    $pre_role = $role;
                }
            }
            // オーナーに編集者が含まれているかチェック
            //
            if( $roles[$i] == "owner" ) {
                if( $types[$i] == "user" ) {
                    array_push( $owners, [ (int)$users[$i] ] );
                } elseif( $types[$i] == "dept" ) {
                    $us = toArrayKeyIncremental( Dept::find( $depts[$i] )->users, 'id' );
                    
                    array_push( $owners, $us );
                } elseif( $types[$i] == "group") {
                    $us = toArrayKeyIncremental( Group::find( $groups[$i] )->users, 'id' );

                    array_push( $owners, $us );
                }
            } 
        }
        // if_debug( $owners );

        // オーナーに編集者が含まれているかチェック
        //
        $owners = Arr::collapse( $owners );
        // if_debug( $owners );
        
        if( ! in_array( $auth->id, $owners )) {
            // if_debug( 'editor is not owner');
            $rules['IacceptNotOwner'] = 'accepted';
        }
        // dd( $orders, $roles, $types );
        // dd( $rules );
        return $rules;
    }
    
    public function messages() {
        $messages = [   'name.required'        => 'アクセスリスト名を入力してください。',
                        'first_owner.required' => "最初のルールは管理者でなければなりません。",
                        'first_input.required' => "１行目の管理者を正しく入力してください。。",
                        #'role_order.required'  => "管理者、編集者、閲覧者、制限閲覧者の順にロールを設定してください。",
                        'role_order.required'  => "管理者の順序は最初に固めてください。",
                        'IacceptNotOwner.accepted'  => "自分自身が管理者設定にありません。以後、このアクセスリストを編集できなくなりますが、よろしいですか。",
            
            ];        
        return $messages;
    }
    
}
