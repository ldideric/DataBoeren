<?php

namespace App\Queries;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;

class UserQuery extends Builder
{
    public function whereCustomer(): self
    {
        return $this->where('role', UserRole::Customer);
    }

    public function whereAdmin(): self
    {
        return $this->where('role', UserRole::Admin);
    }

    public function whereEmployee(): self
    {
        return $this->where('role', UserRole::Employee);
    }

    public function whereRoleIn(UserRole ...$roles): self
    {
        return $this->whereIn('role', $roles);
    }
}
