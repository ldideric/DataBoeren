<?php

namespace App\Booking\Actions;

use App\Booking\Queries\GetExtraAvailability;
use App\Models\Extra;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class ResolveBookingExtras
{
    /**
     * Validate and normalize extra selections from a web request, returning
     * a clean array keyed by extra ID ready for downstream consumption.
     *
     * @param  array<string, int|string>  $quantities  Keyed by extra ID.
     * @return array<array{extra: Extra, quantity: int}>
     */
    public function resolve(array $quantities, Carbon $checkIn, Carbon $checkOut): array
    {
        $wanted = collect($quantities)
            ->map(fn ($quantity) => (int) $quantity)
            ->filter(fn (int $quantity) => $quantity > 0);

        if ($wanted->isEmpty()) {
            return [];
        }

        $extras = Extra::query()->whereKey($wanted->keys()->all())->lockForUpdate()->get()->keyBy('id');

        return $wanted
            ->map(fn (int $quantity, string $id) => [
                'id' => $id,
                'extra' => $extras->get($id),
                'quantity' => $quantity,
            ])
            ->each(fn (array $line) => $this->validateLine($line, $checkIn, $checkOut))
            ->map(fn (array $line) => ['extra' => $line['extra'], 'quantity' => $line['quantity']])
            ->values()
            ->all();
    }

    private function validateLine(array $line, Carbon $checkIn, Carbon $checkOut): void
    {
        $id = $line['id'];
        $extra = $line['extra'];

        if ($extra === null) {
            $this->reject($id, 'Onbekende extra.');
        }

        if ($extra->max_per_booking !== null && $line['quantity'] > $extra->max_per_booking) {
            $this->reject($id, "Maximaal $extra->max_per_booking per boeking.");
        }

        $remaining = GetExtraAvailability::for($extra)->remaining($checkIn, $checkOut);

        if ($remaining !== null && $line['quantity'] > $remaining) {
            $this->reject(
                $id,
                $remaining > 0
                    ? "Nog $remaining beschikbaar voor deze data."
                    : 'Niet beschikbaar voor deze data.',
            );
        }
    }

    private function reject(string $id, string $message): never
    {
        throw ValidationException::withMessages(["extras.$id" => $message]);
    }
}
