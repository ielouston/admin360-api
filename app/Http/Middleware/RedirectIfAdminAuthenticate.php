<?php

namespace Muebleria\Http\Middleware;

use Closure;

class RedirectIfAdminAuthenticate
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
      if (Auth::guard()->check()) {
          return redirect('/home');
      }

      //If request comes from logged in admin, he will
      //be redirected to admin's home page.
      if (Auth::guard('admin')->check()) {
          return redirect('/admin');
      }
      return $next($request);
    }
}
