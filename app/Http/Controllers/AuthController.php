<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }
    public function handleGithubCallback()
    {
        try {
            // GitHub으로부터 사용자 정보 가져오기
            $githubUser = Socialite::driver('github')->user();

            // 사용자 정보가 있는지 확인
            if ($githubUser) {
                // 사용자가 이미 등록된 회원인지 확인
                $user = User::where('name', $githubUser->name)->first();

                if ($user) {
                    // 이미 등록된 회원인 경우 로그인하고 토큰 생성
                    Auth::login($user);
                    $user = Auth::user();
                    $token = $user->createToken('auth_token')->plainTextToken;

                    return response()->json(['token' => $token], 200);
                } else {
                    // 사용자가 회원이 아닌 경우, 회원 가입을 위한 정보 제공
                    return response()->json(['github_user' => $githubUser], 200);
                }
            }
        } catch (\Exception $e) {
            // 예외 처리
            return response()->json(['message' => $e->getMessage() ?? 'Error occurred while processing GitHub callback'], 500);
        }
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'breed' => 'required|string|max:255',
            'age' => 'required|integer|between:1,15',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'breed' => $request->breed,
            'age' => $request->age,
        ]);

        return response()->json(['user' => $user], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['token' => $token], 200);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
