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
    /**
     * @OA\Get(
     *     path="/api/login/github",
     *     summary="GitHub으로 리디렉션",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=302,
     *         description="리디렉션 성공"
     *     )
     * )
     */

    public function redirectToGithub()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * @OA\Get(
     *     path="/api/callback/github",
     *     summary="GitHub 콜백 처리",
     *     tags={"Authentication"},
     *     @OA\Response(
     *         response=200,
     *         description="GitHub 콜백 처리 성공",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="auth_token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="서버 오류",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error occurred while processing GitHub callback")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="사용자 회원가입",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="회원가입 정보",
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "breed", "age"},
     *             @OA\Property(property="name", type="string", example="John"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="breed", type="string", example="Labrador"),
     *             @OA\Property(property="age", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="회원가입 성공",
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="사용자 로그인",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="로그인 정보",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="로그인 성공",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="auth_token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="유효성 검사 오류",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The provided credentials are incorrect.")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="사용자 로그아웃",
     *     tags={"Authentication"},
     *     security={{ "sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="로그아웃 성공",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
