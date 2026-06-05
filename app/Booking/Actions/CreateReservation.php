<?php

namespace App\Booking\Actions;

use App\Booking\BookingValidator;
use App\Booking\Queries\FindAvailableCampsite;
use App\Enums\ReservationSource;
use App\Enums\ReservationStatus;
use App\Enums\UserRole;
use App\Models\Campsite;
use App\Models\Coupon;
use App\Models\Reservation;
use App\Models\User;
use App\Pricing\Actions\CalculatePrice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

readonly class CreateReservation
{
    public function __construct(
        private FindAvailableCampsite $findAvailableCampsite,
        private CalculatePrice $calculatePrice,
        private ResolveBookingExtras $resolveBookingExtras,
        private BookingValidator $validator,
    ) {
    }

    /**
     * @throws ValidationException|Throwable when the campsite is no longer available.
     */
    public function handle(array $data): Reservation
    {
        $checkIn = Carbon::parse($data['check_in']);
        $checkOut = Carbon::parse($data['check_out']);
        $adults = (int) $data['num_adults'];
        $children = (int) $data['num_children'];

        return DB::transaction(function () use ($data, $checkIn, $checkOut, $adults, $children) {
            $campsite = Campsite::query()->whereKey($data['campsite_id'])->first()
                ?? throw ValidationException::withMessages(['campsite_id' => 'De gekozen plek bestaat niet.']);

            $this->validator->validateCapacity($campsite, $adults, $children);

            $campsite = $this->findAvailableCampsite->handle(
                $data['campsite_id'],
                $adults + $children,
                $checkIn,
                $checkOut,
            );

            $coupon = $this->resolveCoupon($data['coupon_code'] ?? null);

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
                'coupon_id' => $coupon?->id,
                'source' => ReservationSource::Online,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'num_adults' => $adults,
                'num_children' => $children,
                'status' => ReservationStatus::Pending,
            ]);
            $reservation->setRelation('customer', $customer)
                ->setRelation('campsite', $campsite)
                ->setRelation('coupon', $coupon);

            $selections = $this->resolveBookingExtras->resolve($data['extras'] ?? [], $checkIn, $checkOut);
            $nights = (int) $checkIn->diffInDays($checkOut);

            foreach ($selections as $line) {
                $reservation->extras()->create([
                    'extra_id' => $line['extra']->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['extra']->price,
                    'subtotal' => CalculatePrice::lineSubtotal($line['extra'], $line['quantity'], $nights),
                ]);
            }

            try {
                $summary = $this->calculatePrice->calculate($reservation, $selections);
                $summary->save();
                $reservation->setRelation('orderSummary', $summary);
            } catch (RuntimeException) {
                throw ValidationException::withMessages([
                    'check_in' => 'Voor de gekozen aankomstdatum is nog geen prijs ingesteld. Kies een andere datum.',
                ]);
            }

            $coupon?->increment('uses_count');

            return $reservation;
        });
    }

    private function resolveCoupon(?string $code): ?Coupon
    {
        if (blank($code)) {
            return null;
        }

        $coupon = Coupon::query()->where('code', $code)->lockForUpdate()->first();

        if ($coupon === null || ! $coupon->isRedeemable()) {
            throw ValidationException::withMessages([
                'coupon_code' => 'Deze couponcode is ongeldig of niet meer geldig.',
            ]);
        }

        return $coupon;
    }
}
