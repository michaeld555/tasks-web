<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Notifications\ReadNotificationsRequest;

class ReadNotificationsController extends Controller
{

    public function __invoke(ReadNotificationsRequest $request)
    {

        dd($request);

    }

}
