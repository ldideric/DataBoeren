<?php

namespace App\Http\Requests;

use App\Enums\CampsiteType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'city' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'campsite_id' => ['nullable', 'string', 'exists:campsites,id'],
            'accommodatietype' => ['required_without:campsite_id', 'nullable', Rule::enum(CampsiteType::class)],
            'num_people' => ['required', 'integer', 'min:1'],
            'aantalkinderen' => ['required', 'integer', 'min:0'],
            'num_plate' => ['required', 'string', 'max:16'],
            'voertuigtype' => ['required', 'string', 'in:auto,camper,caravan'],
            'pay_method' => ['required', 'string', 'in:creditcard,pin,contant'],
            'adult_confirmation' => ['accepted'],
            'huisregels' => ['accepted'],
        ];
    }

    public function accommodatieType(): ?CampsiteType
    {
        $value = $this->validated('accommodatietype');

        return $value ? CampsiteType::from($value) : null;
    }

    public function partySize(): int
    {
        return (int) $this->validated('num_people') + (int) $this->validated('aantalkinderen');
    }
}
