<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'These credentials do not match our records.'], 404);
        }
        return response()->json([
            'token' => $user->createToken($request->getClientIp())->plainTextToken,
            'user' => new UserResource($user)
        ]);
    }
}
