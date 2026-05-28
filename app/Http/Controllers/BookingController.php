<?php

namespace App\Http\Controllers;

use App\Auth\Actions\SendBookingsLink;
use App\Auth\Services\SignedUrlGenerator;
use App\Booking\Actions\CreateReservation;
use App\Booking\DTO\StayCriteria;
use App\Booking\Queries\CheckAvailability;
use App\Booking\Queries\GetAvailableExtras;
use App\Booking\Queries\PreviewPrice;
use App\Enums\ReservationStatus;
use App\Http\Requests\BookingRequest;
use App\Models\Campsite;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(User $user, SignedUrlGenerator $urls): View
    {
        $reservations = $user->reservations()
            ->with('campsite')
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

        return view('bookings.index', [
            'user' => $user,
            'reservations' => $reservations,
            'cancelUrls' => $cancelUrls,
            'paymentUrls' => $paymentUrls,
        ]);
    }

    public function create(
        Request $request,
        CheckAvailability $checkAvailability,
        GetAvailableExtras $getAvailableExtras,
        PreviewPrice $previewPrice,
    ): View|RedirectResponse {
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
            $criteria->vehicles,
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
                    'vehicles' => $criteria->vehicles,
                ])
                ->with('status', 'Deze plek is niet (meer) beschikbaar voor je verblijfsgegevens.');
        }

        return view('bookings.create', [
            'campsite' => $campsite,
            'checkIn' => $criteria->checkIn,
            'checkOut' => $criteria->checkOut,
            'adults' => $criteria->adults,
            'children' => $criteria->children,
            'vehicles' => $criteria->vehicles,
            'order' => $previewPrice->handle($campsite, $criteria->checkIn, $criteria->checkOut, $criteria->adults, $criteria->children),
            'extras' => $getAvailableExtras->handle($criteria->checkIn, $criteria->checkOut),
        ]);
    }

    public function store(BookingRequest $request, CreateReservation $createReservation, SendBookingsLink $sendBookingsLink, SignedUrlGenerator $urls): RedirectResponse
    {
        $reservation = $createReservation->handle($request);

        // Online: send the customer straight to the (signed) Stripe payment page.
        // @todo pay_method is still not persisted (see Reservation model docblock / todo.md).
        if ($request->validated('pay_method') === 'online') {
            return redirect()->to($urls->payment($reservation));
        }

        $sendBookingsLink->handle($reservation->customer);

        return redirect()
            ->route('login.sent')
            ->with('status', 'Uw reservering is ingediend. We hebben u een e-mail gestuurd met een link om uw boeking te bekijken of te annuleren.');
    }

    public function destroy(User $user, Reservation $reservation, SignedUrlGenerator $urls): RedirectResponse
    {
        abort_if($reservation->customer_id !== $user->id, 403);

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
