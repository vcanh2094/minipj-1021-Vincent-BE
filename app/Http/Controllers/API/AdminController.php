<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginAdminRequest;
use App\Http\Requests\RegisterAdminRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AdminController extends Controller
{
    /**
     * register admin
     *
     * @param RegisterAdminRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterAdminRequest $request)
    {
        $validated = $request->validated();
        $admin = Admin::create(array_merge($validated, ['password' => bcrypt($request->password)]));
        return response()->json([
            'message' => 'Admin successfully registered',
            'data' => $admin
        ], 201);
    }

    /**
     * login admin
     *
     * @param LoginAdminRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginAdminRequest $request)
    {
        config()->set( 'auth.defaults.guard', 'admins' );
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
}
