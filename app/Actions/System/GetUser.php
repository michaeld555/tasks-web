<?php

namespace App\Actions\System;

use App\Models\User;

class GetUser
{

    /**
     * Retrieves the user based on the token provided in the request
     *
     * @param string $token
     * @return User|null
     */
    public static function search(string $token = ''): ?User
    {

        $data = TokenDecoder::JWT($token);

        $user = User::where('username', $data['sub'])->first();

        return $user;

    }

}
