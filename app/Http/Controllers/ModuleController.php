<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use App\Traits\SearchableTrait;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    use SearchableTrait;
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'description' => 'required|max:200',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return errorResponse($validator->errors(), 422);
        }
        $module = new Module();
        $module->name = $request->name;
        $module->description = $request->description;
        $module->save();
        return successResponse($module, 'module created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $module = Module::find($id);
        if (!$module) {
            return errorResponse('module not found', 404);
        }
        return successResponse($module, 'module show successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $module = Module::find($id);
        if (!$module) {
            return errorResponse('module not found', 404);
        }
        $rules = array(
            "name" => "required|max:255",
            "description" => "required|max:200"
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $module->name = $request->name;
        $module->description = $request->description;
        $module->save();

        return successResponse($module, 'module update successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $module = Module::find($id);
        $module->delete();
        return successResponse('module delete successfully');
    }

    //search and pagination
    public function index()
    {
        $query = Module::query();
        $searchable_fields = ['name']; 
        $data = $this->serching($query, $searchable_fields);
        return response()->json([
            'success' => true,
            'data' => $data['query']->get(),
            'total' => $data['count']
        ]);
    }
}
