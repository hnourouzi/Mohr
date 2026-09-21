<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Content;
use App\Models\User;

class ContentPolicy
{
    public function view(User $user, Content $content): bool
    {
        return $user->role === UserRole::Admin || $user->id === $content->user_id;
    }

    public function retry(User $user, Content $content): bool
    {
        return $this->view($user, $content) && $content->isRetryable();
    }
}