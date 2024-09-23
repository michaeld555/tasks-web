<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\UpdateSettingsRequest;

class UpdateSettingsController extends Controller
{

    public function __invoke(UpdateSettingsRequest $request)
    {

        dd($request);

    }

}
