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
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:4'
        ]);
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'These credentials do not match our records.'], 404);
        }
        return response()->json([
            'token' => $user->createToken($request->getClientIp())->plainTextToken,
            'user' => new UserResource($user)
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|unique:users',
            'phone' => 'nullable|string|unique:users',
            'gender' => 'required|string|in:male,female',
            'password' => 'required|string|min:4',
            'avatar' => 'nullable|mimes:jpeg,jpg,png|image|max:2048'
        ]);
        $user = User::create($data);
        return response()->json([
            'token' => $user->createToken($request->getClientIp())->plainTextToken,
            'user' => new UserResource($user)
        ]);
    }
}
