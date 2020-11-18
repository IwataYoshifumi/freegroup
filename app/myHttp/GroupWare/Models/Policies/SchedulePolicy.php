<?php

namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;


class SchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) {
        return TRUE;
    }

    public function view(User $user, Schedule $schedule) {
        //
        return TRUE;
    }

    public function create(User $user) {
        //
        // dump( 'SchedulePolicy@create', $user->is_user(), $user->is_admin() ) ;
        return $user->is_user();
    }

    public function update(User $user, Schedule $schedule) {
        return $user->id == $schedule->user->id;
    }

    public function delete(User $user, Schedule $schedule) {
        return $user->id == $schedule->user->id;
    }

    public function restore(User $user, Schedule $schedule) {
        return $user->id == $schedule->user->id;
    }

    public function forceDelete(User $user, Schedule $schedule) {
        return $user->id == $schedule->user->id;
    }
}
