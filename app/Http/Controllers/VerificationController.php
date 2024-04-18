<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;


class VerificationController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function verify(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // 이메일 인증 후 멘토로 업그레이드
        $this->userService->upgradeToMentor($user);

        return response()->json(['message' => 'Email verified successfully'], 200);
    }
}
