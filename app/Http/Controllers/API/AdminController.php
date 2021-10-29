<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginAdminRequest;
use App\Http\Requests\RegisterAdminRequest;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminController extends Controller
{
    /**
     * @param RegisterAdminRequest $request
     * @return JsonResponse
     */
    public function register(RegisterAdminRequest $request)
    {
        JWTAuth::parseToken()->authenticate();
        $admin = Admin::create($request->validated());
        return responder()->success($admin)->respond();
    }

    /**
     * login admin
     *
     * @param LoginAdminRequest $request
     * @return JsonResponse
     */
    public function login(LoginAdminRequest $request)
    {
        config()->set( 'auth.defaults.guard', 'admins' );
        if (!$token = auth()->attempt($request->validated())) {
            return responder()->error(401, 'Invalid email or password')->respond(401);
        }
        return $this->createNewToken($token);
    }

    /**
     * logout admin
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return responder()->success()->respond();
    }

    /**
     * create new token
     *
     * @param $token
     * @return JsonResponse
     */
    protected function createNewToken($token)
    {
        return responder()->success(['access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60,])->respond();
    }
}
