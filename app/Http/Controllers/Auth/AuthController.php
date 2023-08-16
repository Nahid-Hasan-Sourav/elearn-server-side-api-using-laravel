<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(),[
            'name'       => 'required|string',
            'email'      => 'required|unique:users|unique:instructors|unique:admins',
            'password'   => 'required|string|min:6',
            'image'      => 'image | nullable'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'        => 'failed',
                'message'       =>  $validator->messages()->all(),

            ]);
        }
        DB::beginTransaction();
        try{

            if($request->role === 'user'){
         
                $user=User::create([
                    'name'      =>$request->name,
                    'email'     =>$request->email,
                    'password'  =>bcrypt($request->password),
                    'image'     =>$request->image ?? null,
                    'role'     =>$request->role ?? 'user',
                    ]);
                    DB::commit();
                return response()->json([
                    'message'   => 'user registration successfull',
                    'data'      => $user
                ], 200);
            }
    
            if($request->role === 'instructor'){
                $user=Instructor::create([
                    'name'      =>$request->name,
                    'email'     =>$request->email,
                    'password'  =>bcrypt($request->password),
                    'image'     =>$request->image ?? null,
                    'role'     =>$request->role ?? 'instructor',
                    ]);
                    DB::commit();
                return response()->json([
                    'message'   => 'instructor registration successfull',
                    'data'      => $user
                ], 200);
    
            }
            if($request->role === 'admin'){
                $user=Admin::create([
                    'name'      =>$request->name,
                    'email'     =>$request->email,
                    'password'  =>bcrypt($request->password),
                    'image'     =>$request->image ?? null,
                    'role'     =>$request->role ?? 'admin',
                    ]);
                    DB::commit();
                return response()->json([
                    'message'   => 'admin registration successfull',
                    'data'      => $user
                ], 200);
            }

        }
        catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status'    => 'failed',
                'message'   => 'user registraton failed',
                'error_msg' =>$e->getMessage()
            ],500);
        }      
    }
    // public function login(Request $request){
    //   try{
    //     $credentials = $request->only('email', 'password');
    //     $validator = Validator::make($credentials, [
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->errors()], 401);

    //     }
    //     // it will create token
    //     $token = JWTAuth::attempt($request->only('email', 'password'));
    //     // Attempt to authenticate the user
    //     if (!$token) {
    //         return response()->json(['error' => 'Invalid credentials'], 401);
    //     }
    //     // Get the authenticated user
    //     $user = auth()->user();


    //     // Authentication successful, return JWT token
    //     return $this->respondWithToken($token,$user);
    //   }
    //   catch (\Exception $e) {
    //     DB::rollback();
    //     return response()->json([
    //         'status'    => 'failed',
    //         'message'   => 'user registraton failed',
    //         'error_msg' =>  $e->getMessage()
    //     ],500);
    // }      
    // }

    public function login(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
                'role' => 'required', // Make sure the role is one of these values
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            // Find the user based on the provided email and role
            $user = null;
            if ($request->role === 'user') {
                $user = User::where('email', $request->email)->first();
            } elseif ($request->role === 'instructor') {
                $user = Instructor::where('email', $request->email)->first();
            } elseif ($request->role === 'admin') {
                $user = Admin::where('email', $request->email)->first();
            }

            // Attempt to create a JWT token using the user's stored password
            if (!$user || !password_verify($request->password, $user->password)) {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], 401);
            }

            $token = JWTAuth::fromUser($user);

            // Return the JWT token along with user information
            return $this->respondWithToken($token, $user);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Login failed',
                'error_msg' => $e->getMessage()
            ], 500);
        }
    }

    protected function respondWithToken($token, $user) {
        $expiration = config('jwt.ttl'); // Retrieve the token expiration time from configuration

        return response()->json([
            'message'=>'login success',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration * 60,// Convert minutes to seconds
            'user' => $user,
        ]);
    }

    public function me() {
        $user = auth('api')->user();
        return response()->json(['user' => $user]);
    }
}
    
    
    //this function working successfully
    // protected function respondWithToken($token,$user)
    // {
    //     $expiration = config('jwt.ttl'); // Retrieve the token expiration time from configuration

    //     return response()->json([
    //         'user'=>$user,
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         'expires_in' => $expiration * 60 // Convert minutes to seconds
    //     ]);
    // }
    // public function me()
    // {
    //     $user = auth('api')->user();
    //     return response()->json(['user' => $user]);
    // }


