<?php

namespace App\Booking;

use App\Models\Campsite;
use Illuminate\Validation\ValidationException;

class BookingValidator
{
    public function capacityErrors(Campsite $campsite, int $adults, int $children): array
    {
        $errors = [];

        if (($message = $this->peopleError($campsite, $adults, $children)) !== null) {
            $errors['num_children'] = $message;
        }

        return $errors;
    }

    public function peopleError(Campsite $campsite, int $adults, int $children): ?string
    {
        if ($adults + $children <= $campsite->max_people) {
            return null;
        }

        return sprintf(
            'Deze plek biedt plaats aan maximaal %d %s.',
            $campsite->max_people,
            $campsite->max_people === 1 ? 'persoon' : 'personen',
        );
    }

    /**
     * @throws ValidationException
     */
    public function validateCapacity(Campsite $campsite, int $adults, int $children, string $keyPrefix = ''): void
    {
        $errors = $this->capacityErrors($campsite, $adults, $children);

        if ($errors === []) {
            return;
        }

        throw ValidationException::withMessages(
            collect($errors)
                ->mapWithKeys(fn (string $message, string $field): array => [$keyPrefix.$field => $message])
                ->all(),
        );
    }
}
