<?php

namespace App\Http\ViewComposers;

use App\Auth\Services\SignedUrlGenerator;
use App\Http\Middleware\RememberCustomer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NavigationComposer
{
    public function __construct(private readonly SignedUrlGenerator $urls)
    {
    }

    public function compose(View $view): void
    {
        $request = request();
        $isCustomer = (bool) $request->session()->get(RememberCustomer::SESSION_KEY);

        $view->with([
            'myBookingsUrl' => $this->resolveBookingsUrl($request),
            'showLogout' => $isCustomer && $request->routeIs('bookings.index'),
        ]);
    }

    private function resolveBookingsUrl(Request $request): ?string
    {
        $customerId = $request->session()->get(RememberCustomer::SESSION_KEY);

        if (! $customerId) {
            return null;
        }

        $user = User::find($customerId);

        return $user ? $this->urls->bookings($user) : null;
    }
}
