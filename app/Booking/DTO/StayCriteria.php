<?php

namespace App\Booking\DTO;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

final readonly class StayCriteria
{
    public function __construct(
        public ?Carbon $checkIn,
        public ?Carbon $checkOut,
        public ?int $adults,
        public ?int $children,
        public ?int $vehicles,
    ) {}

    public static function fromRequest(Request $request, string $checkInKey = 'datestart', string $checkOutKey = 'dateend'): self
    {
        return new self(
            checkIn: $request->date($checkInKey),
            checkOut: $request->date($checkOutKey),
            adults: $request->filled('adults') ? max(1, $request->integer('adults')) : null,
            children: $request->filled('children') ? max(0, $request->integer('children')) : null,
            vehicles: $request->filled('vehicles') ? max(0, $request->integer('vehicles')) : null,
        );
    }

    public function hasValidDates(): bool
    {
        return $this->checkIn
            && $this->checkOut
            && $this->checkOut->greaterThan($this->checkIn)
            && $this->checkIn->greaterThanOrEqualTo(Carbon::today());
    }

    public function isComplete(): bool
    {
        return $this->hasValidDates()
            && $this->adults !== null
            && $this->children !== null
            && $this->vehicles !== null;
    }

    public function partySize(): int
    {
        return (int) $this->adults + (int) $this->children;
    }
}
