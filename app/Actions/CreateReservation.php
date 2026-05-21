<?php

namespace App\Actions;

use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use App\Enums\UserRole;
use App\Http\Requests\BookingRequest;
use App\Models\Campsite;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreateReservation
{
    /**
     * Find-or-create the customer and store a pending reservation for them,
     * holding a row lock on the campsite for the duration of the transaction
     * so concurrent requests can't double-book the same dates.
     *
     * @throws ValidationException|Throwable when the campsite is no longer available.
     */
    public function handle(BookingRequest $request): Reservation
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
        return DB::transaction(function () use ($request, $data, $checkIn, $checkOut) {
            $campsite = $this->lockAvailableCampsite($request, $checkIn, $checkOut);

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
                ],
            );

            $reservation = Reservation::create([
                'customer_id' => $customer->id,
                'campsite_id' => $campsite->id,
                'source' => ReservationSource::Online,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'num_adults' => (int)$data['num_adults'],
                'num_children' => (int)$data['num_children'],
                'num_vehicles' => $request->vehicleCount(),
                'status' => ReservationStatus::Pending,
            ]);

            // Hand the customer back loaded so callers can mail/redirect without a re-query.
            return $reservation->setRelation('customer', $customer);
        });
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
