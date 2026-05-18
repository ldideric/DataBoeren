<?php

namespace App\Http\Controllers;

use App\Enums\CampsiteType;
use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use App\Enums\UserRole;
use App\Http\Requests\BookingRequest;
use App\Models\Campsite;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(): View
    {
        $reservations = Auth::check()
            ? Auth::user()->reservations()->with('campsite')->latest('check_in')->get()
            : collect();

        return view('bookings.index', [
            'reservations' => $reservations,
        ]);
    }

    public function create(Request $request): View
    {
        $campsite = $request->filled('campsite')
            ? Campsite::find($request->query('campsite'))
            : null;

        return view('bookings.create', [
            'campsiteTypes' => CampsiteType::cases(),
            'campsite' => $campsite,
        ]);
    }

    public function store(BookingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);

        $campsite = $this->resolveCampsite($request, $checkIn, $checkOut);

        if (! $campsite) {
            throw ValidationException::withMessages([
                'campsite_id' => 'De gekozen plek is niet (meer) beschikbaar voor deze data.',
            ]);
        }

        $customer = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'],
                'role' => UserRole::Customer,
                'password' => bcrypt(Str::random(32)),
            ],
        );

        Reservation::create([
            'customer_id' => $customer->id,
            'campsite_id' => $campsite->id,
            'source' => ReservationSource::Online,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'num_people' => $request->partySize(),
            'num_vehicles' => 1,
            'status' => ReservationStatus::Pending,
        ]);

        return redirect()
            ->route('bookings.index')
            ->with('status', 'Uw reservering is ingediend.');
    }

    public function cancelForm(): View
    {
        return view('bookings.cancel');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $customer = User::firstWhere('email', $data['email']);

        if ($customer) {
            $customer->reservations()
                ->whereIn('status', [ReservationStatus::Pending, ReservationStatus::Confirmed])
                ->update([
                    'status' => ReservationStatus::Cancelled,
                    'cancelled_at' => now(),
                    'cancellation_reason' => 'Geannuleerd door klant',
                ]);
        }

        return redirect()
            ->route('home')
            ->with('status', 'Als er actieve reserveringen bij dit e-mailadres horen zijn deze geannuleerd.');
    }

    private function resolveCampsite(BookingRequest $request, Carbon $checkIn, Carbon $checkOut): ?Campsite
    {
        $campsiteId = $request->validated('campsite_id');

        if ($campsiteId) {
            return Campsite::query()
                ->whereKey($campsiteId)
                ->whereFitsParty($request->partySize())
                ->whereAvailableBetween($checkIn, $checkOut)
                ->first();
        }

        return Campsite::query()
            ->whereType($request->accommodatieType())
            ->whereFitsParty($request->partySize())
            ->whereAvailableBetween($checkIn, $checkOut)
            ->first();
    }
}
