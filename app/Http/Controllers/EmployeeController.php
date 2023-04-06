<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\employee;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = array(
            "name" => "required",
            "description" => "required|max:200"
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $employee = employee::create($request->all());
        return successResponse($employee, 'employee data');
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $employee = employee::find($id);
        if (!$employee) {
            return errorResponse('User not found', 404);
        }
        return successResponse($employee, 'employee data show successfully');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $employee = employee::find($request->id);
        $employee->name = $request->name;
        $employee->description = $request->description;
        $employee->salary = $request->salary;
        $employee->save();
        if (!$employee) {
            return errorResponse('User not found', 404);
        }
        return successResponse($employee, 'employee data update successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $employee = employee::find($id);
        $employee->delete();
        return successResponse('employee data delete successfully');
    }
}
