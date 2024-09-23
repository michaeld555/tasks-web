<?php

namespace App\Http\Controllers\Api\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectMembersController extends Controller
{

    public function __invoke(Request $request, $project)
    {

        dd($project);

    }

}
