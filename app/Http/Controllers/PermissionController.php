<?php

namespace App\Http\Controllers;

use App\Models\ModelPermission;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Traits\SearchableTrait;
use Validator;

class PermissionController extends Controller
{
    use SearchableTrait;
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'modules' => 'required|array',
            "modules.*.add_access"      => "required|bool",
            "modules.*.edit_access"     => "required|bool",
            "modules.*.view_access"     => "required|bool",
            "modules.*.delete_access"   => "required|bool",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 403,
                'message' => $validator->errors()->first(),
                'data' => []
            ]);
        }
        $permission = Permission::create($request->only('name', 'description'));
        foreach ($request->input('modules') as $module) {
            $modelpermission = new ModelPermission([
                'module_id' => $module['module_id'],
                'delete_access' => $module['delete_access'],
                'edit_access' => $module['edit_access'],
                'add_access' => $module['add_access'],
                'view_access' => $module['view_access'],
            ]);
            $permission->access()->save($modelpermission);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Permission created successfully',
            'data' => $permission
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function show($id = null)
    {
        $permission =  $id ? Permission::find($id) : Permission::all();
        if (!$permission) {
            return errorResponse('User not found', 404);
        }
        return successResponse($permission, 'Permission show successfully');
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'modules' => 'required|array',
            "modules.*.add_access" => "required|bool",
            "modules.*.edit_access" => "required|bool",
            "modules.*.view_access" => "required|bool",
            "modules.*.delete_access" => "required|bool",
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // find the record to update
        $permission = Permission::find($id);
    
        $permission = Permission::create($request->only('name', 'description'));
        foreach ($request->input('modules') as $module) {
            $modelpermission = new ModelPermission([
                'module_id' => $module['module_id'],
                'delete_access' => $module['delete_access'],
                'edit_access' => $module['edit_access'],
                'add_access' => $module['add_access'],
                'view_access' => $module['view_access'],
            ]);
            $permission->access()->save($modelpermission);
        }
    
        return response()->json(['message' => 'Record updated successfully']);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $id)
    {
        $permission = Permission::find($id);
        $permission->delete();
        return successResponse('Permission delete successfully');
    }

    //search and paginathion
    public function index()
    {
        $query = Permission::query();
        $searchable_fields = ['name']; 
        $data = $this->serching($query, $searchable_fields);
        return response()->json([
            'success' => true,
            'data' => $data['query']->get(),
            'total' => $data['count']
        ]);
    }
}
