<?php

namespace App\Http\Controllers\Api\User;

use App\Actions\System\GetEmployee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UpdateSettingsRequest;
use App\Models\EmployeeSetting;

class UpdateSettingsController extends Controller
{

    public function __invoke(UpdateSettingsRequest $request)
    {

        $employee = GetEmployee::search($request->bearerToken());

        if(is_null($employee)) {
            $this->sendError('Usuário não encontrado', 404);
        }

        $inputs = $request->all();

        $settings = EmployeeSetting::where('employee_id', $employee->id)->first();

        $settings->update($inputs);

        $this->sendResponse([]);

    }

}
