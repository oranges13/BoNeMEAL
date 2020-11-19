<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class VerifyInstalled
{
    /**
     * Exempted routes from middleware
     * @var array
     */
    protected $except = [
        'install*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws  \Exception
     */
    public function handle($request, Closure $next)
    {
        try {
            foreach ($this->except as $excluded_route) {
                if ($request->is($excluded_route)) {
                    Log::debug("Skipping {$request->getUri()} from installed check...");
                    return $next($request);
                }
            }
            if (!file_exists(base_path() . DIRECTORY_SEPARATOR . 'installed.lock'))
            {
                return redirect()->route('install.index');
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $next($request);

    }
}
