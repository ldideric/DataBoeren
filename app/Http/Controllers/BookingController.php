<?php

namespace App\Http\Controllers;

use App\Auth\Services\SignedUrlGenerator;
use App\Booking\DTO\StayCriteria;
use App\Booking\Queries\CheckAvailability;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Models\Campsite;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    public function index(User $user, SignedUrlGenerator $urls): View
    {
        $reservations = $user->reservations()
            ->with(['campsite', 'orderSummary', 'extras.extra', 'coupon', 'payments'])
            ->latest('check_in')
            ->get();

        $cancelUrls = $reservations->mapWithKeys(fn (Reservation $reservation) => [
            $reservation->id => $urls->cancelReservation($user, $reservation),
        ]);

        $paymentUrls = $reservations
            ->where('status', ReservationStatus::Pending)
            ->mapWithKeys(fn (Reservation $reservation) => [
                $reservation->id => $urls->payment($reservation),
            ]);

        $active = $reservations->where('status', '!==', ReservationStatus::Cancelled);

        $stats = [
            'total' => $reservations->count(),
            'upcoming' => $active->filter(fn (Reservation $reservation) => $reservation->check_in->gte(today()))->count(),
            'nights' => $active->sum(fn (Reservation $reservation) => (int) $reservation->check_in->diffInDays($reservation->check_out)),
            'paid' => $reservations
                ->flatMap->payments
                ->where('status', PaymentStatus::Paid)
                ->sum('amount'),
        ];

        return view('bookings.index', [
            'user' => $user,
            'reservations' => $reservations,
            'cancelUrls' => $cancelUrls,
            'paymentUrls' => $paymentUrls,
            'stats' => $stats,
        ]);
    }

    public function create(Request $request, CheckAvailability $checkAvailability): View|RedirectResponse
    {
        $campsite = $request->filled('campsite')
            ? Campsite::find($request->query('campsite'))
            : null;

        if (! $campsite) {
            return redirect()
                ->route('campsites.index')
                ->with('status', 'Kies eerst een kampeerplaats.');
        }

        $criteria = StayCriteria::fromRequest($request, 'check_in', 'check_out');

        if (! $criteria->isComplete()) {
            return redirect()
                ->route('campsites.index')
                ->with('status', 'Vul eerst je verblijfsgegevens in.');
        }

        $fits = $checkAvailability->handle(
            $campsite,
            $criteria->partySize(),
            $criteria->checkIn,
            $criteria->checkOut,
        );

        if (! $fits) {
            return redirect()
                ->route('campsites.index', [
                    'datestart' => $criteria->checkIn->format('Y-m-d'),
                    'dateend' => $criteria->checkOut->format('Y-m-d'),
                    'adults' => $criteria->adults,
                    'children' => $criteria->children,
                ])
                ->with('status', 'Deze plek is niet (meer) beschikbaar voor je verblijfsgegevens.');
        }

        return view('bookings.create', [
            'campsite' => $campsite,
            'checkIn' => $criteria->checkIn,
            'checkOut' => $criteria->checkOut,
            'adults' => $criteria->adults,
            'children' => $criteria->children,
        ]);
    }

    public function destroy(User $user, Reservation $reservation, SignedUrlGenerator $urls): RedirectResponse
    {
        Gate::forUser($user)->authorize('cancel', $reservation);

        if ($reservation->status === ReservationStatus::Cancelled) {
            return redirect()->to($urls->bookings($user));
        }

        $reservation->update([
            'status' => ReservationStatus::Cancelled,
            'cancelled_at' => now(),
            'cancellation_reason' => 'Geannuleerd door klant',
            'cancelled_by_user_id' => $user->id,
        ]);

        return redirect()
            ->to($urls->bookings($user))
            ->with('status', 'Reservering geannuleerd.');
    }
}
