<?php

namespace App\Policies;

class UserPolicy
{
    use Concerns\AdminOnly;
}
