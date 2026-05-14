<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    public function view(User $user, Reservation $model): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    public function update(User $user, Reservation $model): bool
    {
        return $user->isAdmin() || $user->isEmployee();
    }

    public function delete(User $user, Reservation $model): bool
    {
        return $user->isAdmin();
    }
}
