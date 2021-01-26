<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

use App\myHttp\GroupWare\View\Components\InputCustomersComponent;
use App\myHttp\GroupWare\View\Components\InputUsersComponent;
// use App\myHttp\GroupWare\View\Components\InputUserssComponent;
use App\myHttp\GroupWare\View\Components\InputFilesComponent;
use App\myHttp\GroupWare\View\Components\InputFilesComponent2;
use App\myHttp\GroupWare\View\Components\InputSchedulesComponent;

use App\myHttp\GroupWare\View\Components\FindDeptComponent;
use App\myHttp\GroupWare\View\Components\FindUserComponent;
use App\myHttp\GroupWare\View\Components\SelectUserComponent;

use App\myHttp\GroupWare\View\Components\DateSpanFormComponent;

use App\myHttp\GroupWare\View\Components\Dept\DeptsCheckboxComponent;
use App\myHttp\GroupWare\View\Components\User\UsersCheckboxComponent;
use App\myHttp\GroupWare\View\Components\Customer\CustomersCheckboxComponent;


class MyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once( app_path().'/Http/Helpers/BackButton.php' );
        require_once( app_path().'/Http/Helpers/MyForm.php' );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Blade::component( 'input_customers',    InputCustomersComponent::class );
        Blade::component( 'input_users',        InputUsersComponent::class     );
        // Blade::component( 'input_users2',    InputUserssComponent::class     );
        Blade::component( 'input_files',        InputFilesComponent::class     );
        Blade::component( 'input_schedules',    InputSchedulesComponent::class     );
        
        Blade::component( 'find_dept',          FindDeptComponent::class     );
        Blade::component( 'find_user',          FindUserComponent::class     );

        // 2020.11.28 追加
        Blade::component( 'select_user',        SelectUserComponent::class     );   // view groupware.access_list.input で使用
        
        // 2021.01.07 追加（Fileのアップロード、アッタッチ・デタッチフォーム）
        Blade::component( 'input_files2',       InputFilesComponent2::class     );
        
        // 2021.01.18 追加（期間選択フォーム）
        Blade::component( 'input_date_span',     DateSpanFormComponent::class     );

        // 2021.01.21 追加（部署　複数選択フォーム）
        Blade::component( 'checkboxes_depts',     DeptsCheckboxComponent::class     );
        Blade::component( 'checkboxes_users',     UsersCheckboxComponent::class     );
        Blade::component( 'checkboxes_customers', CustomersCheckboxComponent::class     );

    }
}
