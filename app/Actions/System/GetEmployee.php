<?php

namespace App\Actions\System;

use App\Models\Employee;

class GetEmployee
{

    /**
     * Retrieves the employee based on the token provided in the request
     *
     * @param string $token
     * @return Employee|null
     */
    public static function search(string $token = ''): ?Employee
    {

        $user = GetUser::search($token);

        $employee = Employee::with(['user'])->where('user_id', $user->id)->first();

        return $employee;

    }

}
