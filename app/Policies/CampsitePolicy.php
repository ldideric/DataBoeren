<?php

namespace App\Policies;

use App\Models\Campsite;
use App\Models\User;

class CampsitePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    public function view(User $user, Campsite $model): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Campsite $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Campsite $model): bool
    {
        return $user->isAdmin();
    }
}
