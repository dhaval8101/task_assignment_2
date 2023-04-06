<?php

namespace App\Http\Controllers;

use App\Models\ModelPermission;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Traits\SearchableTrait;
use Illuminate\Support\Facades\Validator;

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
     * Show the form for update the specified resource.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required',
            'description' => 'required',
            'modules' => 'required|array',
            "modules.*.module_id" => "required",
            "modules.*.add_access" => "required|boolean",
            "modules.*.edit_access" => "required|boolean",
            "modules.*.view_access" => "required|boolean",
            "modules.*.delete_access" => "required|boolean",
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $permission = Permission::find($id);
        if (!$permission) {
            return response()->json(['error' => 'Record not found'], 404);
        }
        $permission->name = $request->name;
        $permission->description = $request->description;
        $permission->save();
        foreach ($request->input('modules') as $module) {
            $modelpermission = ModelPermission::where('permission_id', $permission->id)
                ->where('module_id', $module['module_id'])
                ->first();
            if (!$modelpermission) {
                $modelpermission = new ModelPermission([
                    'permission_id' => $permission->id,
                    'module_id' => $module['module_id'],
                ]);
            }
            $modelpermission->add_access = $module['add_access'];
            $modelpermission->edit_access = $module['edit_access'];
            $modelpermission->view_access = $module['view_access'];
            $modelpermission->delete_access = $module['delete_access'];
            $modelpermission->save();
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
    /*
    *search and paginathion
    */
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
