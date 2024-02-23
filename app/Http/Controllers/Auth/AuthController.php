<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * @admiral9200
     * @abstract This function is used to register a new user...
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|email:rfc,dns|max:250|unique:users,email',
            'password' => 'required|string|min:6',
            'isAdmin' => 'boolean'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors()
            ], 403);
        }

        $user = User::create([
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(10),
            'isAdmin' => false
        ]);

        $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;

        $response = [
            'status' => 'success',
            'message' => 'User was created successfully!',
            'data' => $data
        ];

        return response()->json($response, 201);
    }    


    /**
     * @admiral9200
     * @abstract This function is used to login a user...
     * @param Illuminate\Http\Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if($validate->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation Error!',
                'data' => $validate->errors()
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(([
                'status' => 'failed',
                'message' => 'Invalid credentials!',
            ]), 401);
        }

        $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;

        $response = [
            'status' => 'success',
            'message' => 'User was logged in successfully!',
            'data' => $data
        ];

        return response()->json($response, 200);
    }

    /**
     * @admiral9200
     * @abstract This function is used to log out a user...
     * @param Illuminate\Http\Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        // auth()->user()->tokens()->delete();

        $response = [
            'status' => 'success',
            'message' => "User was logged out successfully!",
            'data' => ""
        ];

        return response()->json($response, 200);
    }
}
