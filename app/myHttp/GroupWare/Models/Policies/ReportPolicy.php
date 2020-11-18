<?php

namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Report;


class ReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) {
        return TRUE;
    }

    public function view(User $user, Report $report) {
        //
        return TRUE;
    }

    public function create(User $user) {
        //
        // dump( 'ReportPolicy@create', $user->is_user(), $user->is_admin() ) ;
        return $user->is_user();
    }

    public function update(User $user, Report $report) {
        return $user->id == $report->user->id;
    }

    public function delete(User $user, Report $report) {
        return $user->id == $report->user->id;
    }

    public function restore(User $user, Report $report) {
        return $user->id == $report->user->id;
    }

    public function forceDelete(User $user, Report $report) {
        return $user->id == $report->user->id;
    }
}
