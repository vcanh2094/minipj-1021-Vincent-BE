<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeProfileUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use RespondsWithHttpStatus;

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
        return $this->successWithData('user successfully registered', $user, 201);
    }

    /**
     * Login user
     *
     * @param LoginUserRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function login(LoginUserRequest $request)
    {
        $validated = $request->validated();
        if (!$token = auth()->attempt($validated)) {
            return $this->fails('Invalid email or password', 401);
        }
        return $this->create_new_token($token);
    }

    /**
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(){
        auth()->logout();
        return $this->success('user successfully signed out', 200);
    }

    /**
     * Show user profile
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user_profile(){
        return response()->json(auth()->user());
    }

    /**
     * create new token
     *
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function create_new_token($token){
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
    public function change_profile(ChangeProfileUserRequest $request){
        $validated = $request->validated();
        $userId = auth()->user()->id;
        $user = User::query()->where('id', $userId)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'password' => bcrypt($request->new_password),
                ]);
        return $this->success('user successfully changed profile', 200);
    }
}

