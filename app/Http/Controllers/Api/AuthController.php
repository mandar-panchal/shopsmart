<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;



class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return User
     */

     public function createUser(Request $request)
     {    
         try {
             //Validated
             $validateUser = Validator::make($request->all(), 
             [   
                 'name' => 'required|string|max:255',
                 'username' => 'required|max:255|unique:users|regex:/^\S*$/u',
                 'email' => 'required|email|max:255|unique:users',
                 'password' => 'required',
             ]);
 
             if($validateUser->fails()){
                 return response()->json([
                     'status' => false,
                     'message' => 'validation error',
                     'errors' => $validateUser->errors()
                 ], 401);
             }
             DB::beginTransaction();
             $user = User::create([
                 'name' => $request->name,
                 'email' => $request->email,
                 'username'=> $request->username,
                 'password' => Hash::make($request->password),
                 'extension'=> $request->extension,
                 'lead_target'=> $request->lead_target,
                 'incoming_visit_target'=> $request->incoming_visit_target,
                 'popup_visit_target'=> $request->popup_visit_target,
                 'telecalling_target'=> $request->telecalling_target,
             ]);
             $user->assignRole($request->role);
             DB::commit();
             return response()->json([
                 'status' => true,
                 'message' => 'User Created Successfully',
                 'token' => $user->createToken("API TOKEN")->plainTextToken
             ], 200);
 
         } catch (\Throwable $th) {
             DB::rollBack();
             return response()->json([
                 'status' => false,
                 'message' => $th->getMessage()
             ], 500);
         }
     }
    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'email' => 'required',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error all field mandatory!',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            
            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password]) &&
                !Auth::attempt(['username' => $request->email, 'password' => $request->password])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email or Username & Password do not match with our records.',
                ], 401);
            }

            // if(!Auth::attempt($request->only(['email', 'password']))){
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Email & Password does not match with our record.',
            //     ], 401);
            // }
            $user = User::where('email', $request->email)
            ->orWhere('username', $request->email)
            ->first();
            $this->logoutOtherDevices($user, $request->password);
            $intendedUrl = session()->pull('url.intended', '/');
            $token = $user->createToken('app-token')->plainTextToken;

            $user->token = $token;
            $response = ['data' => new UserResource($user)];

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $token,
                'response' => $response,
                'intended_url' => $intendedUrl,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $token = $request->user()->createToken('api-token')->plainTextToken;

            return response()->json(['token' => $token]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    protected function logoutOtherDevices($user, $password)
    {
        $user->forceFill([
            'remember_token' => Str::random(60),
        ])->save();

        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', session()->getId())
            ->delete();
    }

    
    
    public function showCreateForm()
    {
        return view('auth.register');  // Make sure this view exists
    }
    
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));  // Make sure this view exists
    }
    
}
