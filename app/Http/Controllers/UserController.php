<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function profile()
    {
        access(["can-owner", "can-host"]);

        $user = User::find(Auth::id());

        return view("users.profile", [
            "user" => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        access(["can-owner", "can-host"]);

        $data = $request->validate([
            "user" => "required|array",
            "user.account" => "required|string|min:3",
            "user.password" => "nullable|string|min:3",
            "user.email" => "nullable|email",
            "user.phone" => "nullable|string",
        ]);

        $userData = [
            "account" => $data["user"]["account"],
            "email" => $data["user"]["email"],
            "phone" => $data["user"]["phone"],
        ];

        if (!empty($data["user"]["password"])) {
            $userData["password"] = bcrypt(trim($data["user"]["password"]));
            $userData["open_password"] = $data["user"]["password"];
        }

        $user->update($userData);

        note("info", "user:update", "Обновлена учетная запись {$user->account}", User::class, $user->id);

        return back()->with([
            "success" => __("common.saved-success")
        ]);
    }
}
