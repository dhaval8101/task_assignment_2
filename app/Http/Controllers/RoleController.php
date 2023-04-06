<?php

namespace App\Http\Controllers;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use App\Traits\SearchableTrait;

class RoleController extends Controller
{
    use SearchableTrait;
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'permissions' => 'required|array|exists:permissions,id',
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return errorResponse($validator->errors(), 422);
        }
        $role = new Role();
        $role->name = $request->name;
        $role->description = $request->description;
        $role->save();
        $role->permissions()->sync($request->input('permissions'));
        return successResponse($role, 'Role created successfully.');
    }
    /**
     * Display the specified resource.
     */
    public function show($id = null)
    {
        $role = $id ? Role::find($id) : Role::all();
        if (!$role) {
            return errorResponse('User not found', 404);
        }
        return successResponse($role, 'Roles show successfully');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $role = Role::find($request->id);
        $role->name = $request->name;
        $role->description = $request->description;
        $role->save();
        if (!$role) {
            return errorResponse('User not found', 404);
        }
        return successResponse($role, 'roles update successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $role = Role::find($id);
        $role->delete();
        return successResponse('roles delete successfully');
    }
    //search and pagination
    public function index()
    {
        $query = Role::query();
        $searchable_fields = ['name'];
        $data = $this->serching($query, $searchable_fields);
        return response()->json([
            'success' => true,
            'data' => $data['query']->get(),
            'total' => $data['count']
        ]);
    }
}
