<?php

namespace App\Http\Controllers;

use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use App\Enums\UserRole;
use App\Http\Requests\BookingRequest;
use App\Mail\MagicLink;
use App\Models\Campsite;
use App\Models\Reservation;
use App\Models\User;
use App\Support\SignedLink;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

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

    public function create(Request $request): View|RedirectResponse
    {
        $campsite = $request->filled('campsite')
            ? Campsite::find($request->query('campsite'))
            : null;

        if (! $campsite) {
            return redirect()
                ->route('campsites.index')
                ->with('status', 'Kies eerst een kampeerplaats.');
        }

        $checkIn = $request->date('check_in');
        $checkOut = $request->date('check_out');
        $adults = $request->filled('adults') ? max(1, $request->integer('adults')) : null;
        $children = $request->filled('children') ? max(0, $request->integer('children')) : null;
        $vehicles = $request->filled('vehicles') ? max(0, $request->integer('vehicles')) : null;

        $datesOk = $checkIn
            && $checkOut
            && $checkOut->greaterThan($checkIn)
            && $checkIn->greaterThanOrEqualTo(Carbon::today());

        if (! $datesOk || $adults === null || $children === null || $vehicles === null) {
            return redirect()
                ->route('campsites.index')
                ->with('status', 'Vul eerst je verblijfsgegevens in.');
        }

        $fits = Campsite::query()
            ->whereKey($campsite->id)
            ->whereFitsParty($adults + $children, $vehicles)
            ->whereAvailableBetween($checkIn, $checkOut)
            ->exists();

        if (! $fits) {
            return redirect()
                ->route('campsites.index', [
                    'datestart' => $checkIn->format('Y-m-d'),
                    'dateend' => $checkOut->format('Y-m-d'),
                    'adults' => $adults,
                    'children' => $children,
                    'vehicles' => $vehicles,
                ])
                ->with('status', 'Deze plek is niet (meer) beschikbaar voor je verblijfsgegevens.');
        }

        return view('bookings.create', [
            'campsite' => $campsite,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'adults' => $adults,
            'children' => $children,
            'vehicles' => $vehicles,
        ]);
    }

    public function store(BookingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);

        /**
         * @todo Postgres prod: replace this app-level lock with a database-level
         *       exclusion constraint:
         *         CREATE EXTENSION btree_gist;
         *         ALTER TABLE reservations ADD CONSTRAINT reservations_no_overlap
         *           EXCLUDE USING gist (
         *             campsite_id WITH =,
         *             daterange(check_in, check_out, '[)') WITH &&
         *           ) WHERE (status IN ('pending', 'confirmed') AND deleted_at IS NULL);
         *       The lockForUpdate() approach below works on both MySQL and Postgres
         *       but only protects writes that go through this code path — Filament
         *       admin saves, Tinker, etc. can still double-book.
         */
        [$user, $reservation] = DB::transaction(function () use ($request, $data, $checkIn, $checkOut) {
            $campsite = $this->lockAvailableCampsite($request, $checkIn, $checkOut);

            if (! $campsite) {
                throw ValidationException::withMessages([
                    'campsite_id' => 'De gekozen plek is niet (meer) beschikbaar voor deze data.',
                ]);
            }

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'],
                    'role' => UserRole::Customer,
                ],
            );

            $reservation = Reservation::create([
                'customer_id' => $user->id,
                'campsite_id' => $campsite->id,
                'source' => ReservationSource::Online,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'num_adults' => (int)$data['num_adults'],
                'num_children' => (int)$data['num_children'],
                'num_vehicles' => $request->vehicleCount(),
                'status' => ReservationStatus::Pending,
            ]);

            return [$user, $reservation];
        });

        // Online: send the customer straight to the (signed) Stripe payment page.
        // @todo pay_method is still not persisted (see Reservation model docblock / todo.md).
        if ($data['pay_method'] === 'online') {
            return redirect()->to(SignedLink::payment($reservation));
        }

        // Pay on location: mail a signed link so the customer can manage the booking later.
        Mail::to($user->email)->send(new MagicLink($user, SignedLink::bookings($user)));

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

    private function lockAvailableCampsite(BookingRequest $request, Carbon $checkIn, Carbon $checkOut): ?Campsite
    {
        return Campsite::query()
            ->whereKey($request->validated('campsite_id'))
            ->whereFitsParty($request->partySize(), $request->vehicleCount())
            ->whereAvailableBetween($checkIn, $checkOut)
            ->lockForUpdate()
            ->first();
    }
}
