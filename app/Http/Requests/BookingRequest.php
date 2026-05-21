<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'campsite_id' => ['required', 'string', 'exists:campsites,id'],
            'num_adults' => ['required', 'integer', 'min:1'],
            'num_children' => ['required', 'integer', 'min:0'],
            'num_vehicles' => ['required', 'integer', 'min:0'],
            'pay_method' => ['required', 'string', 'in:online,in_person'],
            'adult_confirmation' => ['accepted'],
            'house_rules' => ['accepted'],
        ];
    }

    public function partySize(): int
    {
        return (int) $this->validated('num_adults') + (int) $this->validated('num_children');
    }

    public function vehicleCount(): int
    {
        return (int) $this->validated('num_vehicles');
    }
}
