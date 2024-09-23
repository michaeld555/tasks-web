<?php

namespace App\Http\Controllers\Api\User;

use App\Actions\System\GetEmployee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UpdatePreferencesRequest;

class UpdatePreferencesController extends Controller
{

    public function __invoke(UpdatePreferencesRequest $request)
    {

        $employee = GetEmployee::search($request->bearerToken());

        if(is_null($employee)) {
            $this->sendError('Usuário não encontrado', 404);
        }

        $inputs = $request->all();

        $employee->update($inputs);

        $this->sendResponse([]);

    }

}
