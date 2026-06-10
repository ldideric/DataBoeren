<?php

namespace App\Queries;

use Illuminate\Database\Eloquent\Builder;

class CouponQuery extends Builder
{
    public function redeemable(): self
    {
        return $this
            ->where(fn (self $query) => $query->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()))
            ->where(fn (self $query) => $query->whereNull('max_uses')->orWhereColumn('uses_count', '<', 'max_uses'));
    }
}
