<?php

namespace App\Http\Middleware;

use Closure;

class VerifyNotInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (file_exists(base_path() . DIRECTORY_SEPARATOR . 'installed.lock'))
        {
            abort(401, 'Application already installed.');
        }

        return $next($request);
    }
}
