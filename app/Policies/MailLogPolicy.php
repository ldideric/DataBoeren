<?php

namespace App\Policies;

use App\Models\MailLog;
use App\Models\User;

class MailLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, MailLog $mailLog): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, MailLog $mailLog): bool
    {
        return false;
    }

    public function delete(User $user, MailLog $mailLog): bool
    {
        return false;
    }

    public function restore(User $user, MailLog $mailLog): bool
    {
        return false;
    }

    public function forceDelete(User $user, MailLog $mailLog): bool
    {
        return false;
    }
}
