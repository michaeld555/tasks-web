<?php

namespace App\Actions\System;

use App\Models\RecentActivity as RecentActivityModel;
use Illuminate\Support\Facades\Auth;

class RecentActivity
{

    /**
     * Logs the recent activity performed by the user in the system
     *
     * @param String $message
     * @param [type] $user
     * @return void
     */
    public static function create(String $message, $user = null): void
    {

        $user = is_null($user) ? Auth::user() : $user;

        RecentActivityModel::create([
            'message' => $message,
            'user_id' => $user->id
        ]);

    }

}
