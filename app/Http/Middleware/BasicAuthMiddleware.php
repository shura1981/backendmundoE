<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class BasicAuthMiddleware
{

    private $auth = "Y2tfZGJjMDI5ZTA2ZWJmZTdmNjg5YjJmZTRiOGJkNzhjNWEyNzlhN2IxYjpjc180ODhjOTNjOTlhOTE3OTc4NzU4N2Y0NmIzYmIyNWZkYzNmYzdlZDBj";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle(Request $request, Closure $next)
    {
        // Verifica si el encabezado "Authorization" existe en la solicitud.
        if (!$request->header('Authorization')) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        // Obtiene las credenciales del encabezado "Authorization".
        $credentials = base64_decode(trim(str_replace('Basic', '', $request->header('Authorization'))));

        // Verifica las credenciales con tu lógica de autenticación.
        // En este ejemplo, las credenciales son usuario:password, pero puedes cambiarlo según tu lógica.
        $validCredentials = base64_encode($credentials) === $this->auth;

        if (!$validCredentials) {
            return response()->json(['message' => 'Unauthorized.', 'aut' => $this->auth, 'credentials' => base64_encode($credentials)], 401);
        }
        return $next($request);
    }
}