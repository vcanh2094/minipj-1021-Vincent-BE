<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeProfileUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Traits\RespondsWithHttpStatus;
use Flugg\Responder\Responder;
use Illuminate\Http\JsonResponse;
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
     * Register user.
     *
     * @param RegisterUserRequest $request
     * @param Responder $responder
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request, Responder $responder)
    {
        $validated = $request->validated();
        $user = User::create(array_merge($validated, ['password' => bcrypt($request->password)]));
        return responder()->success($user)->respond();
    }

    /**
     * Login user.
     *
     * @param LoginUserRequest $request
     * @param Responder $responder
     * @return JsonResponse
     */
    public function login(LoginUserRequest $request, Responder $responder): JsonResponse
    {
        $validated = $request->validated();
        if (!$token = auth()->attempt($validated)) {
            return responder()->error(401, 'Invalid email or password')->respond();
        }else{
            return $this->create_new_token($token);
        }

    }

    /**
     * Logout user
     *
     * @return JsonResponse
     */
    public function logout(Responder $responder){
        auth()->logout();
        return $responder->success()->respond();
    }

    /**
     * Show user profile
     *
     * @return JsonResponse
     */
    public function user_profile(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    /**
     * Create new token
     *
     * @param $token
     * @return JsonResponse
     */
    protected function create_new_token($token): JsonResponse
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'user_id' => auth()->user()->id,
            'user_name' => auth()->user()->name,
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    /**
     * change user profile
     *
     * @param ChangeProfileUserRequest $request
     * @return JsonResponse
     */
    public function change_profile(ChangeProfileUserRequest $request, Responder $responder){
        $validated = $request->validated();
        $userId = auth()->user()->id;
        if($request->isChangePassword == true){
            $user = User::query()->where('id', $userId)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
                'password' => bcrypt($request->new_password),
            ]);
        }else{
            $user = User::query()->where('id', $userId)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'birthday' => $request->birthday,
            ]);
        }

        return responder()->success(User::query()->where('id', $userId))->respond();
    }
}

