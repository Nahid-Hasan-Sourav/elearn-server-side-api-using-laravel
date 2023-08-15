<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // public function register(Request $request){

    //     $user=User::create([
    //     'name'=>$request->name,
    //     'email'=>$request->email,
    //     'password'=>$request->password,
    //     'image'=>null,
    //     ]);

    //   return response()->json([
    //   'message'=>'success',
    //   'data'=>$user

    //   ],200);
    // }
    public function register(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'image' => 'nullable|string', // Validation rule for the nullable image field
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'image' => $validatedData['image'], // You can use null here if image is not provided
        ]);

        return response()->json([
            'message' => 'success',
            'data' => $user
        ], 200);
    }
    // public function login(Request $request){
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
    //         // Get the authenticated user
    //     $user = auth('api')->user();


    //     // Authentication successful, return JWT token
    //     return $this->respondWithToken($token,$user);
    // }
    // //this function working successfully
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
    public function login(Request $request)
{

    $credentials = $request->only('email', 'password');
    $validator = Validator::make($credentials, [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 401);
    }

    // Attempt to create a JWT token
    $token = JWTAuth::attempt($credentials);

    if (!$token) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    // Get the authenticated user
    $user = auth()->user();

    // Return the response with token and user information
    return $this->respondWithToken($token, $user);
}

protected function respondWithToken($token, $user)
{
    $expiration = config('jwt.ttl'); // Retrieve the token expiration time from configuration

    return response()->json([
        'user' => $user,
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => $expiration * 60 // Convert minutes to seconds
    ]);
}



    public function me()
    {
        $user = auth('api')->user();
        return response()->json(['user' => $user]);
    }

}
