<?php

namespace Friparia\Admin;

use Closure;
use Auth;

class Middleware
{

    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        if (Auth::check()){
            return $next($request);
        }
        return redirect()->route('admin.login')->with('error', '请登录!');
    }


}