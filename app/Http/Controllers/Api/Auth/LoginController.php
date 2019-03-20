<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'origin' => 'required|in:google,facebook',
            'socialId' => 'required',
            'email' => 'required|email',
            'names' => 'required',
            'access_token' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errors($validator);
        }

        $insertedUser = "{}";
        DB::transaction(function () use ($request, &$insertedUser) {
            $insertedUser = User::updateOrCreate([
                'origin' => $request->post("origin"),
                'social_id' => $request->post("socialId"),
                'email' => $request->post("email"),
                'names' => $request->post("names"),
                'avatar' => $request->post("avatar")
            ]);

            UserToken::insert([
                'user_id' => $insertedUser->id,
                'token' => $request->post('access_token'),
            ]);
        });

        return $insertedUser;
    }

    public function logout(Request $request) {

    }
}