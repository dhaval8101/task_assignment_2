<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show($id = null)
    {
        $user = $id ? User::find($id) : User::all();
        if (!$user) {
            return errorResponse('User not found', 404);
        }
        return successResponse($user, 'user show successfully');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = User::find($request->id);
        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->phonenumber = $request->phonenumber;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();

        if (!$user) {
            return errorResponse('User not found', 404);
        }
        return successResponse($user, 'user update successfully');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();
        return successResponse('user delete successfully');
    }
    //Logout user
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "message" => "User successfully logged out",
        ]);
        return response()->json(['message' => 'Logged out successfully']);
    }

    //Change Password 
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
        ]);
        $user = Auth::user();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'Password changed successfully',
            ]);
        }
        return response()->json([
            'message' => 'Invalid current password',
        ], 400);
    }
}
