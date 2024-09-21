<?php

namespace App\Actions\System;

class TokenDecoder
{

    public static function JWT(string $jwt = '') {

        $parts = explode('.', $jwt);

        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);

        return $payload;

    }

}
