<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeProfileUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register user.
     *
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $user = User::create(array_merge($request->validated(), ['password' => bcrypt($request->password)]));
        return responder()->success($user)->respond();
    }

    /**
     * Login user.
     *
     * @param LoginUserRequest $request
     * @return JsonResponse
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        if (!$token = auth()->attempt($request->validated())) {
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
    public function logout(): JsonResponse
    {
        auth()->logout();
        return responder()->success()->respond();
    }

    /**
     * Show user profile
     *
     * @return JsonResponse
     */
    public function user_profile(): JsonResponse
    {
        return responder()->success(auth()->user())->respond();
    }

    /**
     * refresh access token
     *
     * @return JsonResponse
     */
    public function refresh() {
        return $this->create_new_token(auth()->refresh());
    }


    /**
     * Create new access token
     *
     * @param $token
     * @return JsonResponse
     */
    protected function create_new_token($token)
    {
        return responder()->success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user_id' => auth()->user()->id,
            'user_name' => auth()->user()->name,
            'expires_in' => auth()->factory()->getTTL() * 60,
        ])->respond();
    }

    /**
     * change user profile
     *
     * @param ChangeProfileUserRequest $request
     * @return JsonResponse
     */
    public function change_profile(ChangeProfileUserRequest $request): JsonResponse
    {
        $request->validated();
        $userId = auth()->user()->id;
        if($request->isChangePassword == true){
            if(Hash::check($request->old_password, auth()->user()->password)){
                User::query()->where('id', $userId)
                    ->update(array_merge(
                        $request->validated(),
                        ['password' => bcrypt($request->new_password_confirmation)]
                    ));
            }else{
                return responder()->error('incorrect', 'old password is incorrect')->respond();
            }
        }else{
            User::query()->where('id', $userId)->update($request->validated());
        }
        return responder()->success(User::query()->where('id', $userId))->respond();
    }
}

