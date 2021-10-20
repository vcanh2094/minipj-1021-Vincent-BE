<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginAdminRequest;
use App\Http\Requests\RegisterAdminRequest;
use App\Models\Admin;
use Flugg\Responder\Responder;
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
        return responder()->success($admin)->respond();
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
            return responder()->error('401', 'Invalid email or password')->respond(401);
        }
        return $this->create_new_token($token);
    }

    /**
     * create new token
     *
     * @param $token
     * @return JsonResponse
     */
    protected function create_new_token($token): JsonResponse
    {
        return responder()->success(['access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60,])->respond();
    }
}
