<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginAdminRequest;
use App\Http\Requests\RegisterAdminRequest;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class AdminController extends Controller
{
    /**
     * register admin
     *
     * @param RegisterAdminRequest $request
     * @return JsonResponse
     */
    public function register(RegisterAdminRequest $request): JsonResponse
    {
        $admin = Admin::create(array_merge($request->validated(), ['password' => bcrypt($request->password)]));
        return response()->json([
            'message' => 'Admin successfully registered',
            'data' => $admin
        ], 201);
    }

    /**
     * login admin
     *
     * @param LoginAdminRequest $request
     * @return JsonResponse
     */
    public function login(LoginAdminRequest $request): JsonResponse
    {
        config()->set( 'auth.defaults.guard', 'admins' );
        if (!$token = auth()->attempt($request->validated())) {
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
     * @return JsonResponse
     */
    protected function createNewToken($token): JsonResponse
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
