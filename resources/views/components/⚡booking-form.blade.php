<?php

use App\Booking\Actions\CreateReservation;
use App\Booking\Actions\RecordCashPayment;
use App\Booking\Queries\GetAvailableExtras;
use App\Booking\Queries\PreviewBookingPrice;
use App\Auth\Services\SignedUrlGenerator;
use App\Mail\BookingReceived;
use App\Models\Campsite;
use App\Models\Coupon;
use App\Models\OrderSummary;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class () extends Component {
    public string $campsiteId;
    public string $checkIn;
    public string $checkOut;
    public int $adults = 1;
    public int $children = 0;
    public int $vehicles = 0;

    public array $extras = [];
    public string $couponCode = '';
    public string $appliedCoupon = '';

    public string $firstName = '';
    public string $lastName = '';
    public string $phone = '';
    public string $email = '';
    public string $payMethod = '';
    public bool $adultConfirmation = false;
    public bool $houseRules = false;

    public function mount(Campsite $campsite, string $checkIn, string $checkOut, int $adults, int $children, int $vehicles): void
    {
        $this->campsiteId = $campsite->id;
        $this->checkIn = $checkIn;
        $this->checkOut = $checkOut;
        $this->adults = $adults;
        $this->children = $children;
        $this->vehicles = $vehicles;
    }

    protected function rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:255'],
            'payMethod' => ['required', 'in:online,in_person'],
            'couponCode' => ['nullable', 'string', 'max:255'],
            'adultConfirmation' => ['accepted'],
            'houseRules' => ['accepted'],
            'extras' => ['array'],
            'extras.*' => ['integer', 'min:0'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'firstName' => 'voornaam',
            'lastName' => 'achternaam',
            'phone' => 'telefoonnummer',
            'email' => 'e-mailadres',
            'payMethod' => 'betaalmethode',
            'adultConfirmation' => 'leeftijdsbevestiging',
            'houseRules' => 'akkoord met de privacyverklaring',
        ];
    }

    #[Computed]
    public function campsite(): Campsite
    {
        return Campsite::findOrFail($this->campsiteId);
    }

    #[Computed]
    public function availableExtras(): Collection
    {
        return app(GetAvailableExtras::class)->handle(
            Carbon::parse($this->checkIn),
            Carbon::parse($this->checkOut),
        );
    }

    #[Computed]
    public function order(): ?OrderSummary
    {
        $coupon = $this->resolveAppliedCoupon();

        return app(PreviewBookingPrice::class)->fromFormData([
            'campsite_id' => $this->campsiteId,
            'check_in' => $this->checkIn,
            'check_out' => $this->checkOut,
            'num_adults' => $this->adults,
            'num_children' => $this->children,
            'coupon_id' => $coupon?->isRedeemable() ? $coupon->id : null,
            'extras' => $this->extraRows(),
        ]);
    }

    #[Computed]
    public function couponNotice(): ?array
    {
        if ($this->appliedCoupon === '') {
            return null;
        }

        $coupon = $this->resolveAppliedCoupon();

        if ($coupon === null || ! $coupon->isRedeemable()) {
            return ['ok' => false, 'text' => 'Deze couponcode is ongeldig of niet meer geldig.'];
        }

        if (! $this->order?->coupon_discount) {
            return ['ok' => false, 'text' => 'Coupon is geldig, maar levert geen korting op voor deze boeking.'];
        }

        return ['ok' => true, 'text' => "Coupon toegepast: {$coupon->title} ({$coupon->formatted_discount})."];
    }

    public function applyCoupon(): void
    {
        $this->appliedCoupon = trim($this->couponCode);
    }

    public function submit(CreateReservation $createReservation, RecordCashPayment $recordCashPayment, SignedUrlGenerator $urls)
    {
        $this->validate();

        $this->appliedCoupon = trim($this->couponCode);

        $reservation = $createReservation->handle([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
            'email' => $this->email,
            'campsite_id' => $this->campsiteId,
            'check_in' => $this->checkIn,
            'check_out' => $this->checkOut,
            'num_adults' => $this->adults,
            'num_children' => $this->children,
            'num_vehicles' => $this->vehicles,
            'coupon_code' => $this->appliedCoupon ?: null,
            'extras' => $this->extras,
        ]);

        if ($this->payMethod === 'online') {
            return $this->redirect($urls->payment($reservation));
        }

        $recordCashPayment->handle($reservation);

        Mail::to($reservation->customer->email)
            ->send(new BookingReceived($reservation, $urls->bookings($reservation->customer)));

        session()->flash('status', 'Uw reservering is ingediend. We hebben u een e-mail gestuurd met een link om uw boeking te bekijken of te annuleren.');

        return $this->redirect(route('login.sent'));
    }

    private function resolveAppliedCoupon(): ?Coupon
    {
        if ($this->appliedCoupon === '') {
            return null;
        }

        return Coupon::query()->where('code', $this->appliedCoupon)->first();
    }

    private function extraRows(): array
    {
        return collect($this->extras)
            ->map(fn ($quantity, $id): array => ['extra_id' => (string) $id, 'quantity' => (int) $quantity])
            ->values()
            ->all();
    }
}; ?>

@php
    $checkIn = \Illuminate\Support\Carbon::parse($this->checkIn);
    $checkOut = \Illuminate\Support\Carbon::parse($this->checkOut);
@endphp

<div class="mx-auto w-full max-w-2xl px-6 py-8">
    <div class="rounded-2xl border border-tan-400 bg-tan-300 p-6 shadow-sm ring-1 ring-black/5">
        <h1 class="text-xl font-bold text-olivegreen-400">Invulformulier</h1>

        @if ($errors->any())
            <div class="mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <p class="font-semibold mb-1">✗ Controleer de volgende velden:</p>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit="submit" class="mt-6 space-y-6">
            <div class="space-y-2 rounded-lg border border-tan-500 bg-tan-200 px-4 py-3 text-sm text-black">
                <div>
                    Gekozen plek: <strong>{{ $this->campsite->name }}</strong>
                    ({{ \Illuminate\Support\Str::headline($this->campsite->type->value) }})
                </div>
                <div>
                    Verblijf:
                    <strong>{{ $checkIn->format('d M Y') }}</strong> t/m
                    <strong>{{ $checkOut->format('d M Y') }}</strong>
                </div>
                <div>
                    Groep:
                    <strong>{{ $adults }}</strong> {{ $adults === 1 ? 'volwassene' : 'volwassenen' }},
                    <strong>{{ $children }}</strong> {{ $children === 1 ? 'kind' : 'kinderen' }},
                    <strong>{{ $vehicles }}</strong> {{ $vehicles === 1 ? 'voertuig' : 'voertuigen' }}.
                </div>
                <div class="pt-1">
                    <a href="{{ route('campsites.index', [
                        'datestart' => $checkIn->format('Y-m-d'),
                        'dateend' => $checkOut->format('Y-m-d'),
                        'adults' => $adults,
                        'children' => $children,
                        'vehicles' => $vehicles,
                    ]) }}" class="underline hover:no-underline">Wijzig verblijfsgegevens</a>
                </div>
            </div>

            @if ($this->availableExtras->isNotEmpty())
                <fieldset>
                    <legend class="w-full border-b border-olivegreen-800 pb-2 text-sm font-semibold text-black">Extra's</legend>

                    <div class="mt-4 space-y-3">
                        @foreach ($this->availableExtras as $row)
                            @php
                                $extra = $row['model'];
                                $cap = $row['cap'];
                                $perNight = $extra->billing_type->value === 'per_night';
                            @endphp
                            <div class="flex items-center justify-between gap-4" wire:key="extra-{{ $extra->id }}">
                                <div class="text-sm">
                                    <span class="font-medium text-black">{{ $extra->name }}</span>
                                    <span class="text-black">— € {{ number_format($extra->price / 100, 2, ',', '.') }} {{ $perNight ? 'per nacht' : 'eenmalig' }}</span>
                                    @if ($extra->description)
                                        <p class="text-xs text-black">{{ $extra->description }}</p>
                                    @endif
                                    @if ($cap === 0)
                                        <p class="text-xs text-red-600">Uitverkocht voor deze data</p>
                                    @endif
                                </div>
                                <input type="number" wire:model.live.debounce.400ms="extras.{{ $extra->id }}"
                                    min="0" @if ($cap !== null) max="{{ $cap }}" @endif @disabled($cap === 0)
                                    class="w-20 rounded-lg border border-olivegreen-500 bg-tan-200 px-3 py-2 text-sm focus:border-olivegreen-400 focus:outline-none focus:ring-2 focus:ring-olivegreen-400">
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            @endif

            <div class="rounded-lg border border-tan-500 bg-tan-200 px-4 py-3">
                <h2 class="text-sm font-semibold text-olivegreen-400">Prijsoverzicht</h2>
                <div class="mt-2">
                    @if ($this->order)
                        @include('partials.price-breakdown', [
                            'order' => $this->order,
                            'adults' => $adults,
                            'children' => $children,
                        ])
                    @else
                        <p class="text-sm text-black">Vul je gegevens in om de prijs te berekenen.</p>
                    @endif
                </div>
            </div>

            <fieldset>
                <legend class="w-full border-b border-olivegreen-800 pb-2 text-sm font-semibold text-black">Persoonsgegevens</legend>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="block text-sm text-black">Voornaam*</label>
                        <input type="text" id="first_name" wire:model="firstName" required class="mt-1 w-full rounded-lg border border-olivegreen-500 bg-tan-200 px-3 py-2 text-sm focus:border-olivegreen-400 focus:outline-none focus:ring-2 focus:ring-olivegreen-400">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm text-black">Achternaam*</label>
                        <input type="text" id="last_name" wire:model="lastName" required class="mt-1 w-full rounded-lg border border-olivegreen-500 bg-tan-200 px-3 py-2 text-sm focus:border-olivegreen-400 focus:outline-none focus:ring-2 focus:ring-olivegreen-400">
                    </div>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="phone" class="block text-sm text-black">Telefoonnummer*</label>
                        <input type="tel" id="phone" wire:model="phone" required class="mt-1 w-full rounded-lg border border-olivegreen-500 bg-tan-200 px-3 py-2 text-sm focus:border-olivegreen-400 focus:outline-none focus:ring-2 focus:ring-olivegreen-400">
                    </div>
                    <div>
                        <label for="email" class="block text-sm text-black">E-mailadres*</label>
                        <input type="email" id="email" wire:model="email" required class="mt-1 w-full rounded-lg border border-olivegreen-500 bg-tan-200 px-3 py-2 text-sm focus:border-olivegreen-400 focus:outline-none focus:ring-2 focus:ring-olivegreen-400">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend class="w-full border-b border-olivegreen-800 pb-2 text-sm font-semibold text-black">Couponcode</legend>

                <div class="mt-4">
                    <label for="coupon_code" class="block text-sm text-black">Heeft u een couponcode? (optioneel)</label>
                    <div class="mt-1 flex gap-2">
                        <input type="text" id="coupon_code" wire:model="couponCode" wire:keydown.enter.prevent="applyCoupon"
                            class="w-full flex-1 rounded-lg border border-olivegreen-500 bg-tan-200 px-3 py-2 text-sm uppercase focus:border-olivegreen-400 focus:outline-none focus:ring-2 focus:ring-olivegreen-400"
                            placeholder="BIJV. ZOMER25">
                        <button type="button" wire:click="applyCoupon"
                            class="shrink-0 rounded-lg border-2 bg-cerulean-300 border-cerulean-400 px-4 py-2 text-sm font-semibold text-cerulean-900 transition hover:border-cerulean-400 hover:bg-cerulean-400">
                            Toepassen
                        </button>
                    </div>
                    @if ($this->couponNotice)
                        <p class="mt-1 text-xs {{ $this->couponNotice['ok'] ? 'text-olivegreen-400' : 'text-red-600' }}">{{ $this->couponNotice['text'] }}</p>
                    @endif
                    <p class="mt-1 text-xs text-black">De korting wordt direct in het prijsoverzicht verrekend.</p>
                </div>
            </fieldset>

            <fieldset>
                <legend class="w-full border-b border-olivegreen-800 pb-2 text-sm font-semibold text-black">Betaalmethode</legend>

                <div class="mt-4">
                    <label for="pay_method" class="block text-sm text-black">Betaalmethode*</label>
                    <div class="relative mt-1">
                        <select id="pay_method" wire:model="payMethod" required class="w-full appearance-none rounded-lg border border-olivegreen-500 bg-tan-200 px-3 py-2 pr-8 text-sm focus:border-olivegreen-400 focus:outline-none focus:ring-2 focus:ring-olivegreen-400">
                            <option value="">Selecteer</option>
                            <option value="online">Online betalen (Stripe)</option>
                            <option value="in_person">Betalen op locatie</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-olivegreen-600">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend class="w-full border-b border-olivegreen-800 pb-2 text-sm font-semibold text-black">Akkoord</legend>

                <div class="mt-4 space-y-2 text-sm text-black">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <div class="relative flex shrink-0 items-center">
                            <input type="checkbox" wire:model="adultConfirmation" value="1" required class="peer h-4 w-4 appearance-none rounded border-2 border-olivegreen-600 bg-tan-200 checked:border-olivegreen-500 checked:bg-olivegreen-500 focus:outline-none focus:ring-2 focus:ring-olivegreen-400 cursor-pointer transition">
                            <svg class="pointer-events-none absolute inset-0 hidden h-4 w-4 text-white peer-checked:block" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z"/>
                            </svg>
                        </div>
                        Ik ben 18 jaar of ouder*
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <div class="relative flex shrink-0 items-center">
                            <input type="checkbox" wire:model="houseRules" value="1" required class="peer h-4 w-4 appearance-none rounded border-2 border-olivegreen-600 bg-tan-200 checked:border-olivegreen-500 checked:bg-olivegreen-500 focus:outline-none focus:ring-2 focus:ring-olivegreen-400 cursor-pointer transition">
                            <svg class="pointer-events-none absolute inset-0 hidden h-4 w-4 text-white peer-checked:block" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z"/>
                            </svg>
                        </div>
                        <div>Ik ga akkoord met de <a href="{{ route('privacy') }}" target="_blank" class="font-semibold"><span class="underline hover:no-underline">privacyverklaring</span>*</a></div>
                    </label>
                </div>
            </fieldset>

            <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                class="w-full rounded-lg border-2 bg-cerulean-300 border-cerulean-400 py-2 text-sm font-semibold text-cerulean-900 transition hover:border-cerulean-400 hover:bg-cerulean-400 disabled:opacity-60">
                <span wire:loading.remove wire:target="submit">Reservering indienen</span>
                <span wire:loading wire:target="submit">Bezig met indienen…</span>
            </button>
        </form>
    </div>
</div>
