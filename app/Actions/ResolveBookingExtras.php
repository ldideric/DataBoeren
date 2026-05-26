<?php

namespace App\Actions;

use App\Models\Extra;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class ResolveBookingExtras
{
    /**
     * @param  array<string, int|string>  $quantities  extra id => requested quantity
     * @return array<array{extra: Extra, quantity: int}>
     */
    public function resolve(array $quantities, Carbon $checkIn, Carbon $checkOut): array
    {
        $wanted = array_filter(array_map('intval', $quantities), fn (int $quantity) => $quantity > 0);

        if ($wanted === []) {
            return [];
        }

        $extras = Extra::query()->whereKey(array_keys($wanted))->lockForUpdate()->get()->keyBy('id');

        $selections = [];

        foreach ($wanted as $id => $quantity) {
            $extra = $extras->get($id) ?? $this->reject($id, 'Onbekende extra.');

            if ($extra->max_per_booking !== null && $quantity > $extra->max_per_booking) {
                $this->reject($id, "Maximaal {$extra->max_per_booking} per boeking.");
            }

            $available = $extra->availableStockBetween($checkIn, $checkOut);

            if ($available !== null && $quantity > $available) {
                $this->reject($id, $available > 0
                    ? "Nog {$available} beschikbaar voor deze data."
                    : 'Niet beschikbaar voor deze data.');
            }

            $selections[] = ['extra' => $extra, 'quantity' => $quantity];
        }

        return $selections;
    }

    private function reject(string $id, string $message): never
    {
        throw ValidationException::withMessages(["extras.{$id}" => $message]);
    }
}
