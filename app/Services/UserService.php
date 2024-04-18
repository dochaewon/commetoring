<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function upgradeToMentor(User $user)
    {
        $user->update(['type' => 'mentor']);
    }
}
