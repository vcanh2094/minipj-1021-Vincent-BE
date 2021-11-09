<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
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
        User::query()->where('id', $user)->delete();
        return responder()->success()->respond();
    }
}
