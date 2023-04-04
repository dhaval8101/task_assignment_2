<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckEmployeeAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $employeeType, $access)
    {
        $user = auth()->user();
    // dd($user);
        // Check if user has the required access
    if ($user->hasAccess($employeeType, $access)) {
            return $next($request);
        }
        return errorResponse('Unauthorized access.',403);
    }
    
}


