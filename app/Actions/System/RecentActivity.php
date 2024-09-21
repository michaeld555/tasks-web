<?php

namespace App\Actions\System;

use App\Models\RecentActivity as RecentActivityModel;
use Illuminate\Support\Facades\Auth;

class RecentActivity
{

    public static function create(String $message, $user = null): void
    {

        $user = is_null($user) ? Auth::user() : $user;

        RecentActivityModel::create([
            'message' => $message,
            'user_id' => $user->id
        ]);

    }

}
