use Illuminate\Support\Facades\Route;

$route = Route::getRoutes()->match($request);
$currentroute = $route->getName()   






mkdir app/Helpers
touch app/Helpers/ApiHelper.php
<?php

namespace App\Helpers;

class ApiHelper
{
    public static function successResponse($data, $message = null)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function errorResponse($message, $code)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}
///

return ApiHelper::successResponse($users);

use App\Helpers\ApiHelper;




public function handle(Request $request, Closure $next)
{
    // Get logged in user
    $user = auth()->user();

    // Get current called API route
    $route = app('router')->getRoutes()->match($request);
    $currentroute = $route->getName();
    $routes = explode('.', $currentroute);
    $apimodule = $routes[0] ?? null;
    $apiMode = $routes[1] ?? null;

    // Check if user has permission for current route
    $permission = Permission::where('name', $apiMode . '_access')->first();
    $module = Module::where('name', $apimodule)->first();
    $permissionmodel = Permisionmodel::where('permission_id', $permission->id)
        ->where('module_id', $module->id)
        ->where('role_id', $user->role_id) // Assuming user has a role_id property
        ->first();

    // Return error response if user does not have permission
    if (!$permissionmodel || !$permissionmodel->{$apiMode . '_access'}) {
        return response()->json(["error" => 'Access Forbidden'], 403);
    }

    return $next($request);
}




/////




<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Permisionmodel;
use Symfony\Component\Routing\Route;

class CheckEmployeeAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next)
    {
        // Get logged in user
        $user = auth()->user();

        // Define user permissions
        $userpermissions = [
            [
                'module' => 'role',
                'add_access' => false,
                'view_access' => true,
                'edit_access' => false,
                'delete_access' => true,
            ],
            [
                'module' => 'permission',
                'add_access' =>true,
                'view_access' => true,
                'edit_access' => true,
                'delete_access' => false,
            ],

            [
                'module' => 'module',
                'add_access' => true,
                'view_access' => true,
                'edit_access' => true,
                'delete_access' => true,
            ],
            // Add more permissions here as needed
        ];

        // Get current called API route
        $route = app('router')->getRoutes()->match($request);
        $currentroute = $route->getName();
        $routes = explode('.', $currentroute);
        $apimodule = $routes[0] ?? null;
        $apiMode = $routes[1] ?? null;
        
        $isValid = false;

        // Check if user has permission for current route
        foreach ($userpermissions as $value) {
            if ($value['module'] == $apimodule) {
                if (isset($value[$apiMode . '_access']) && $value[$apiMode . '_access']) {
                    $isValid = true;
                }
            }
        }

        // Return error response if user does not have permission
        if (!$isValid) {
            return response()->json(["error" => 'Access Forbidden'], 403);
        }

        return $next($request);
    }
}

/////



<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Permisionmodel;
use Symfony\Component\Routing\Route;

class CheckEmployeeAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

     public function handle(Request $request, Closure $next)
     {
         // Get logged in user
         $user = auth()->user();
     
         // Define user permissions
         $permissions = Permission::where('user_id', $user->id)->get();
     
         // Get current called API route
         $route = app('router')->getRoutes()->match($request);
         $currentroute = $route->getName();
         $routes = explode('.', $currentroute);
         $apimodule = $routes[0] ?? null;
         $apiMode = $routes[1] ?? null;
         
         $isValid = false;
     
         // Check if user has permission for current route
         foreach ($permissions as $permission) {
             if ($permission->module == $apimodule) {
                 if (isset($permission->{$apiMode . '_access'}) && $permission->{$apiMode . '_access'}) {
                     $isValid = true;
                 }
             }
         }
     
         // Return error response if user does not have permission
         if (!$isValid) {
             return response()->json(["error" => 'Access Forbidden'], 403);
         }
     
         return $next($request);
     }
    }     