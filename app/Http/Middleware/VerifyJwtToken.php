<?php

namespace App\Http\Middleware;

use App\Actions\System\TokenDecoder;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyJwtToken
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $token = $request->bearerToken();

        if (!$token) {
            $this->InvalidJwtException();
        }

        $data = TokenDecoder::JWT($token);

        if ($data['exp'] < time()) {
            $this->InvalidJwtException();
        }

        return $next($request);

    }

    /**
     * exception for unauthenticated users
     *
     * @return void
     */
    public function InvalidJwtException()
    {

        $response = [
            'code' => 401,
            'success' => false,
            'message' => 'Token JWT inválido ou expirado',
        ];

        throw new HttpResponseException(response()->json($response, 401));

    }

}
