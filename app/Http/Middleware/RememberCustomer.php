<?php

namespace App\Http\Middleware;

use App\Models\Reservation;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RememberCustomer
{
    public const SESSION_KEY = 'customer_id';

    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();

        $user = match ($route?->getName()) {
            'bookings.index', 'bookings.destroy' => $route->parameter('user'),
            'payments.show', 'payments.checkout' => $this->customerOf($route->parameter('reservation')),
            default => null,
        };

        if ($user instanceof User) {
            $request->session()->put(self::SESSION_KEY, $user->id);
        }

        return $next($request);
    }

    private function customerOf(mixed $reservation): ?User
    {
        return $reservation instanceof Reservation ? $reservation->customer : null;
    }
}
