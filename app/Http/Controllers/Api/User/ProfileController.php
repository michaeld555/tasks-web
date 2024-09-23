<?php

namespace App\Http\Controllers\Api\User;

use App\Actions\System\GetEmployee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function __invoke(Request $request)
    {

        $employee = GetEmployee::search($request->bearerToken());

        if(is_null($employee)) {
            $this->sendError('Usuário não encontrado', 404);
        }

        $data = [
            'name' => $employee->full_name,
            'email' => $employee->email,
            'username' => $employee->user->username,
            'profile_photo' => $employee->profile_photo,
            'user_id' => $employee->user_id,
            'redmine_id' => $employee->redmine_id,
            'redmine_token' => $employee->redmine_token,
            'notifications' => (bool) $employee->notifications,
            'alerts' => (bool) $employee->alerts,
        ];

        return $this->sendResponse($data);

    }

}
