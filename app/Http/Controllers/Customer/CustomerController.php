<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use DB;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer as Customer;

class CustomerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request ) {
        //  初期値の設定
        //
        // if_debug( $request->all() );
        if( isset( $request['find'] )) {
            $find = $request['find'];
            $sort = $request['sort'];
            $show = $request[ 'show' ];
            $asc_desc = $request['asc_desc'];
        } else {
            $find = [ 'paginate' => 20 ];
            $sort = [];
            $show = [ 'address', 'moblie' ];
            $asc_desc = [];
        }
        $show = array_merge( $show, [ 'name', 'email' ]);
        
        // 検索実行
        //
        $customers = Customer::search( $find, $sort, $asc_desc );
        //　戻るボタンの戻先設定
        //
        BackButton::setHere( $request );
        
        // 表示ビューの切り替え
        //
        $view =  Route::currentRouteName();
        // dd( $customers );
        // return view( $view )->with( 'customers', $customers )
        return view( 'customer.index' )->with( 'customers', $customers )
                                       ->with( 'find' , $find  )
                                       ->with( 'sort' , $sort  )
                                       ->with( 'show' , $show  )
                                       ->with( 'asc_desc', $asc_desc );
    }
    
    public function select( Request $request ) {
        return $this->index( $request );
    }
    
    public function csv( Request $request ) {
        if( isset( $request['find'] )) {
            $find = $request['find'];
            $sort = $request['sort'];
            $asc_desc = $request['asc_desc'];
        } else {
            $find = [ 'retired' => false ];
            $sort = [];
            $asc_desc = [];
        }        
        $find['pagination'] = 0;

        $customers = Customer::search( $find, $sort, $asc_desc )->toArray();

        return OutputCSV::input_array( [ 'lists' => $customers ] );
    }

    public function create() {
        // return view( 'customer.create' );
        return view( 'customer.input' );
    }

    public function store( CustomerRequest $request ) {
        
        $customer = DB::transaction( function() use( $request ) {
            $customer = new Customer();
        
            $customer->name     = $request['name'];
            $customer->kana     = $request['kana'];
            $customer->email    = $request['email'];
            $customer->zip_code = $request['zip_code'];
            $customer->prefecture = $request['prefecture'];
            $customer->city     = $request['city'];
            $customer->street  = $request['street'];
            $customer->building = $request['building'];
            $customer->tel      = $request['tel'];
            $customer->fax      = $request['fax'];
            $customer->mobile   = $request['mobile'];
            $customer->birth_day = $request['birth_day'];
            $customer->sex      = $request['sex'];
            $customer->memo     = $request['memo'];
            if( config( 'customer.salseforce.enable' )) {
               $customer->salseforce_id = $request['salseforce_id'];
            }
            
            $customer->save();
            return $customer;
        }); 

        session()->flash( 'flash_message', "顧客". $request['name']. "を追加しました。" );
        session()->regenerateToken();
        return redirect()->route('customer.show', [ 'customer' => $customer ]);
        // return redirect()->route('user.home', [ 'customer' => $customer ]);
    }

    public function show(Customer $customer) {
        //
        BackButton::stackHere( request() );
        return view( 'customer.show' )->with( 'customer', $customer );
    }

    public function edit( Customer $customer) {
        // public function edit( CustomerRequest $request, Customer $customer) {
        // if_debug( 'EDIT ');
        // if_debug( $customer );
        BackButton::stackHere( request() );
        return view( 'customer.input' )->with( 'customer', $customer );

    }

    public function update( CustomerRequest $request, Customer $customer) {
        
        $customer->name     = $request['name'];
        $customer->kana     = $request['kana'];
        $customer->email    = $request['email'];
        $customer->zip_code = $request['zip_code'];
        $customer->prefecture = $request['prefecture'];
        $customer->city     = $request['city'];
        $customer->street  = $request['street'];
        $customer->building = $request['building'];
        $customer->tel      = $request['tel'];
        $customer->fax      = $request['fax'];
        $customer->mobile   = $request['mobile'];
        $customer->birth_day = $request['birth_day'];
        $customer->sex      = $request['sex'];
        $customer->memo     = $request['memo'];
        if( config( 'customer.salseforce.enable' )) {
            $customer->salseforce_id = $request['salseforce_id'];
        }
        
        $customer->save();
        // dd( $request, $customer );
        Session::flash( 'flash_message', "顧客情報を変更しました" );
        session()->regenerateToken();
        BackButton::removePreviousSession();
        return redirect()->route( 'customer.show', [ 'customer' => $customer ] );
    }
    
    public function delete(Customer $customer) {
        //
        BackButton::stackHere( request() );
        return view( 'customer.delete')->with( 'customer', $customer );
    }
    public function deleted( Customer $customer ) {

        $customer->delete();

        session()->regenerateToken();
        BackButton::removePreviousSession();
        return view( 'customer.delete')->with( 'customer', $customer );
    }
    
    public function json( Request $request ) {
        $name = $request->name;
        // dd( $name );
        
        if( is_null( $name )) { return response()->json( [] ); }
        
        $customers = Customer::selectRaw( '*' )->where(   'name', 'like', '%'.$name.'%' );
        if( preg_match( '/^[ア-ヾ]+$/', $name ) ) {
            $customers = $customers->orWhere( 'kana', 'like', '%'.$name.'%' );
        }
        // if_debug( $customers );
        $customers = $customers->get();
        
        $array = [];
        foreach( $customers as $c ) {
            $address = $c->city.$c->street;
            if( ! empty( $c->building )) { $address .= " ( ".$c->building." )"; }
            $age = ( $c->age() ) ? $c->age()  : "";
            // if_debug( $c->age() );
            array_push( $array, [   'id' => $c->id, 
                                    'name' => $c->name, 
                                    'kana' => $c->kana, 
                                    'address' => $address, 
                                    'age' => $age, 
                                    'prefecture' => $c->prefecture, 
                                    'city' => $c->city, 
                                    'street' => $c->street,
                                    'building' => $c->building 
                                    ] );
            
        }

        return response()->json( $array );
        // return response()->json( $customers );
        // dd( $customers, response()->json( $customers ));
    }
    
    // パスワード変更画面
    //
    public function password( ) {
        
        $customer = auth('customer')->user();
        return View( 'customer.password' )->with( 'customer', $customer );
        
    }
    
    // パスワード変更実行
    //
    public function updatePassword( CustomerRequest $request ) {
        
        $customer = auth('customer')->user();
        if( empty( $customer ) ) { abort( 403, 'CustomerController.updatePassword: エラー'); }
        $customer = DB::transaction( function() use( $customer, $request ) {
            $customer->password = Hash::make( $request['password'] ) ;
            $customer->save();
            return $customer;
        }); 

        Session::flash( 'flash_message', "パスワードを変更しました。" );
        session()->regenerateToken();
        return redirect()->route('customer.change_password' );
        // return view( 'customer.password' );
        // return redirect()->route( 'customer.home');        
    }
}
