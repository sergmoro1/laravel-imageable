<?php
 
namespace Sergmoro1\Imageable\Http\Middleware;
 
use Illuminate\Support\Facades\Auth;
 
class AuthenticateOnceWithBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        return Auth::onceBasic() ?: $next($request);
    }
 
}
