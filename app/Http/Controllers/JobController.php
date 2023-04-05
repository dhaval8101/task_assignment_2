<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Validator;
class JobController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = array(
            "name" => "required",
            "salary" => "required"
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $employee = Job::create($request->all());
        return successResponse($employee, 'Job data store successfully');
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $employee = Job::find($id);
        if (!$employee) {
            return errorResponse('User not found', 404);
        }
        return successResponse($employee, 'Job data show successfully');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $job = Job::find($request->id);
        $job->name = $request->name;
        $job->salary = $request->salary;
        $job->save();
        if (!$job) {
            return errorResponse('User not found', 404);
        }
        return successResponse($job, 'job data update successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $job = Job::find($id);
        $job->delete();
        return successResponse('job data delete successfully');
    }
}