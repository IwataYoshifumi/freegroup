<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Models\Policies\SchedulePolicy;
use App\myHttp\GroupWare\Models\Policies\ReportPolicy;
use App\myHttp\GroupWare\Models\Policies\FilePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        
        Schedule::class => SchedulePolicy::class,
        Report::class   => ReportPolicy::class,
        MyFile::class   => FilePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        
    }
}
