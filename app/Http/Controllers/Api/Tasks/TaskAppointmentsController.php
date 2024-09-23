<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskAppointmentsController extends Controller
{

    public function __invoke(Request $request, $taskId)
    {

        dd($taskId);

    }

}
