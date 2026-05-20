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
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(): View
    {
        $reservations = Auth::user()
            ->reservations()
            ->with('campsite')
            ->latest('check_in')
            ->get();

        return view('bookings.index', [
            'reservations' => $reservations,
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

        return view('bookings.create', [
            'campsite' => $campsite,
        ]);
    }

    public function store(BookingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $wasGuest = ! Auth::check();

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
        $user = DB::transaction(function () use ($request, $data, $checkIn, $checkOut) {
            $campsite = $this->lockAvailableCampsite($request, $checkIn, $checkOut);

            if (! $campsite) {
                throw ValidationException::withMessages([
                    'campsite_id' => 'De gekozen plek is niet (meer) beschikbaar voor deze data.',
                ]);
            }

            $user = Auth::user() ?? User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'],
                    'province' => $request->province(),
                    'role' => UserRole::Customer,
                ],
            );

            if ($user->province !== $request->province()) {
                $user->province = $request->province();
                $user->save();
            }

            Reservation::create([
                'customer_id' => $user->id,
                'campsite_id' => $campsite->id,
                'source' => ReservationSource::Online,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'num_people' => $request->partySize(),
                'num_vehicles' => 1,
                'status' => ReservationStatus::Pending,
            ]);

            return $user;
        });

        if ($wasGuest && $user->role === UserRole::Customer) {
            $url = URL::temporarySignedRoute(
                'login.verify',
                now()->addMinutes(15),
                ['user' => $user->id],
            );

            Mail::to($user->email)->send(new MagicLink($user, $url));

            return redirect()
                ->route('login')
                ->with('status', 'Uw reservering is ingediend. We hebben u een inloglink gemaild om uw boekingen te beheren.');
        }

        return redirect()
            ->route('bookings.index')
            ->with('status', 'Uw reservering is ingediend.');
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        abort_if($reservation->customer_id !== Auth::id(), 403);

        if ($reservation->status === ReservationStatus::Cancelled) {
            return redirect()->route('bookings.index');
        }

        $reservation->update([
            'status' => ReservationStatus::Cancelled,
            'cancelled_at' => now(),
            'cancellation_reason' => 'Geannuleerd door klant',
            'cancelled_by_user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('status', 'Reservering geannuleerd.');
    }

    private function lockAvailableCampsite(BookingRequest $request, Carbon $checkIn, Carbon $checkOut): ?Campsite
    {
        return Campsite::query()
            ->whereKey($request->validated('campsite_id'))
            ->whereFitsParty($request->partySize())
            ->whereAvailableBetween($checkIn, $checkOut)
            ->lockForUpdate()
            ->first();
    }
}
