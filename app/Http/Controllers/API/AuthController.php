<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeProfileUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register user
     *
     * @param RegisterUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::create(array_merge($validated, ['password' => bcrypt($request->password)]));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Login user
     *
     * @param LoginUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginUserRequest $request)
    {
        $validated = $request->validated();
        if (!$token = auth()->attempt($validated)) {

            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid Email or Password',
                ], 401);
        }
        return $this->createNewToken($token);
    }

    /**
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(){
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * show user profile
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile(){
        return response()->json(auth()->user());
    }

    /**
     * create new token
     *
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    /**
     * change user profile
     *
     * @param ChangeProfileUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeProfile(ChangeProfileUserRequest $request){
        $validated = $request->validated();
        $userId = auth()->user()->id;
        $user = User::where('id', $userId)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'password' => bcrypt($request->new_password),
                ]
        );
        return response()->json([
            'message' => 'User successfully changed profile',
            'userID' => $userId
        ]);
    }
}

