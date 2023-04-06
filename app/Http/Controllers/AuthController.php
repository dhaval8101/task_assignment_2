<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Validator;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Str;
use App\Traits\SearchableTrait;

class AuthController extends Controller
{
    use SearchableTrait;
    //user Ragister
    public function store(Request $request)
    {
        $rules = array(
            "name" => "required",
            "lastname" => "required",
            "phonenumber" => "required",
            "email" => "required",
            "password" => "required||min:8",
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id'
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {

            return $validator->errors();
        } 
        $user = new User();
        $user->name = $request->name;
        $user->lastname = $request->lastname;
        $user->phonenumber = $request->phonenumber;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        // $token = $user->createToken('Token')->plainTextToken;
        $user->roles()->sync($request->input('roles'));

        // return the user and API token as a response
            // return response()->json([
            //     'user' => $user,
            //     'token' => $token
            // ], 201);
        return successResponse($user, 'user data');
    }
    //email and password valid
    public function login(Request $request)
    {
        $rules = array(
        "email" => "required",
        "password" => "required||min:8",
    );
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {

        return $validator->errors();
    } 
        $request->only('email', 'password');
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('Token')->plainTextToken;
            return response()->json([
                'user' => $user,
                'token' => $token
            ]);
        }
        return response()->json([
            'message' => 'Invalid user data'
        ], 401);
    }
    //forogt password link send mailtrap
    public function forgotPasswordLink(Request $request)
    {
        $validaiton = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);
        if ($validaiton->fails())
            return $validaiton->errors();
        $user = User::where('email', $request->email)->first();
        $token = Str::random(16);
        $user->notify(new ResetPasswordNotification($token));
        PasswordReset::create([
            'token' => $token,
            'email' => $request->email
        ]);
        return "Mail Sent";
    }
    //forgot password
    public function forgotPassword(Request $request)
    {
        $validaiton = Validator::make($request->all(), [
            'token' => 'required|exists:password_resets,token',
            'email' => 'required|exists:password_resets,email|exists:users,email',
            'password'  => 'required|min:8|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validaiton->fails())
            return $validaiton->errors();
        $passwordReset = PasswordReset::where('token', $request->token)->first();
        $user = User::where('email', $passwordReset->email)->first();
        $user->update([
            'password'  => Hash::make($request->password)
        ]);
        return 'Password Changed Successfully';
    }
    //searching and pagination
    public function index()
    {
        $query = User::query();
        $searchable_fields = ['name']; 
        $data = $this->serching($query, $searchable_fields);
        return response()->json([
            'success' => true,
            'data' => $data['query']->get(),
            'total' => $data['count']
        ]);
    }
}
