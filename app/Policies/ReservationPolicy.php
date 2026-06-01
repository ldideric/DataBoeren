<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function cancel(User $user, Reservation $model): bool
    {
        return $user->id === $model->customer_id;
    }

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

    public function restore(User $user, Reservation $model): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Reservation $model): bool
    {
        return $user->isAdmin();
    }
}
