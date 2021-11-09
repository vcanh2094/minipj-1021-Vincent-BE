<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::query();
        return responder()->success($users)->respond();
    }

    public function delete($user)
    {
        Address::query()->where('user_id', $user)->delete();
        Favorite::query()->where('user_id', $user)->delete();
        Order::query()->where('user_id', $user)->delete();
        User::query()->where('id', $user)->delete();
        return responder()->success()->respond();
    }
}
