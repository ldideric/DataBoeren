<?php

namespace App\Http\Controllers;

use App\Actions\CalculatePrice;
use App\Actions\CreateReservation;
use App\Actions\SendBookingsLink;
use App\Enums\ReservationStatus;
use App\Http\Requests\BookingRequest;
use App\Models\Campsite;
use App\Models\OrderSummary;
use App\Models\Reservation;
use App\Models\User;
use App\Support\SignedLink;
use App\Support\StayCriteria;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class BookingController extends Controller
{
    public function index(User $user): View
    {
        $reservations = $user->reservations()
            ->with('campsite')
            ->latest('check_in')
            ->get();

        $cancelUrls = $reservations->mapWithKeys(fn (Reservation $reservation) => [
            $reservation->id => SignedLink::cancelReservation($user, $reservation),
        ]);

        return view('bookings.index', [
            'user' => $user,
            'reservations' => $reservations,
            'cancelUrls' => $cancelUrls,
        ]);
    }

    public function create(Request $request, CalculatePrice $calculatePrice): View|RedirectResponse
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

        $fits = Campsite::query()
            ->whereKey($campsite->id)
            ->whereFitsParty($criteria->partySize(), $criteria->vehicles)
            ->whereAvailableBetween($criteria->checkIn, $criteria->checkOut)
            ->exists();

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
            'order' => $this->previewPrice($calculatePrice, $campsite, $criteria),
        ]);
    }

    private function previewPrice(CalculatePrice $calculatePrice, Campsite $campsite, StayCriteria $criteria): ?OrderSummary
    {
        $preview = (new Reservation([
            'check_in' => $criteria->checkIn,
            'check_out' => $criteria->checkOut,
            'num_adults' => $criteria->adults,
            'num_children' => $criteria->children,
        ]))->setRelation('campsite', $campsite);

        try {
            return $calculatePrice->handle($preview);
        } catch (RuntimeException) {
            return null;
        }
    }

    public function store(BookingRequest $request, CreateReservation $createReservation, SendBookingsLink $sendBookingsLink): RedirectResponse
    {
        $reservation = $createReservation->handle($request);

        // Online: send the customer straight to the (signed) Stripe payment page.
        // @todo pay_method is still not persisted (see Reservation model docblock / todo.md).
        if ($request->validated('pay_method') === 'online') {
            return redirect()->to(SignedLink::payment($reservation));
        }

        $sendBookingsLink->handle($reservation->customer);

        return redirect()
            ->route('login.sent')
            ->with('status', 'Uw reservering is ingediend. We hebben u een e-mail gestuurd met een link om uw boeking te bekijken of te annuleren.');
    }

    public function destroy(User $user, Reservation $reservation): RedirectResponse
    {
        abort_if($reservation->customer_id !== $user->id, 403);

        if ($reservation->status === ReservationStatus::Cancelled) {
            return redirect()->to(SignedLink::bookings($user));
        }

        $reservation->update([
            'status' => ReservationStatus::Cancelled,
            'cancelled_at' => now(),
            'cancellation_reason' => 'Geannuleerd door klant',
            'cancelled_by_user_id' => $user->id,
        ]);

        return redirect()
            ->to(SignedLink::bookings($user))
            ->with('status', 'Reservering geannuleerd.');
    }
}
