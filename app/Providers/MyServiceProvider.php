<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

use App\myHttp\GroupWare\View\Components\InputCustomersComponent;
use App\myHttp\GroupWare\View\Components\InputUsersComponent;
// use App\myHttp\GroupWare\View\Components\InputUserssComponent;
use App\myHttp\GroupWare\View\Components\InputFilesComponent;
use App\myHttp\GroupWare\View\Components\InputSchedulesComponent;

use App\myHttp\GroupWare\View\Components\FindDeptComponent;
use App\myHttp\GroupWare\View\Components\FindUserComponent;

class MyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // dd( app_path().'Http/Myclass/BackButtonClass.php' );
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
    }
}
