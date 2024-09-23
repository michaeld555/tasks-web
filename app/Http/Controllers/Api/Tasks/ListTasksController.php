<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListTasksController extends Controller
{

    public function __invoke(Request $request, $project)
    {

        dd($project);

    }

}
