<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Policies\AccessListPolicy;

use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\ReporList;
use App\myHttp\GroupWare\Models\ReporProp;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

use App\myHttp\GroupWare\Models\Policies\UserPolicy;
use App\myHttp\GroupWare\Models\Policies\GroupPolicy;
use App\myHttp\GroupWare\Models\Policies\DeptPolicy;
use App\myHttp\GroupWare\Models\Policies\FilePolicy;

use App\myHttp\GroupWare\Models\Policies\CalendarPolicy;
use App\myHttp\GroupWare\Models\Policies\CalPropPolicy;
use App\myHttp\GroupWare\Models\Policies\SchedulePolicy;

use App\myHttp\GroupWare\Models\Policies\ReportPolicy;
use App\myHttp\GroupWare\Models\Policies\ReportListPolicy;
use App\myHttp\GroupWare\Models\Policies\ReportPropPolicy;

use App\myHttp\GroupWare\Models\Policies\TaskPolicy;
use App\myHttp\GroupWare\Models\Policies\TaskListPolicy;
use App\myHttp\GroupWare\Models\Policies\TaskPropPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        
        User::class         => UserPolicy::class,
        Dept::class         => DeptPolicy::class,
        Group::class        => GroupPolicy::class,
        
        AccessList::class   => AccessListPolicy::class,

        Calendar::class     => CalendarPolicy::class,
        CalProp::class      => CalPropPolicy::class,
        Schedule::class     => SchedulePolicy::class,
        
        Report::class       => ReportPolicy::class,
        ReportList::class   => ReportListPolicy::class,
        ReportProp::class   => ReportPropPolicy::class,

        Task::class         => TaskPolicy::class,
        TaskList::class     => TaskListPolicy::class,
        TaskProp::class     => TaskPropPolicy::class,

        MyFile::class       => FilePolicy::class,
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
