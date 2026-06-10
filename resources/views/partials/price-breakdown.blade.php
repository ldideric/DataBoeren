@php
    // Amounts are integer cents; format to euros only at display time.
    $euro = fn ($cents) => '€ ' . number_format($cents / 100, 2, ',', '.');

    $nights = $order->num_nights;
    $accommodation = $order->nightly_rate * $nights
        + $order->per_adult_rate * $adults * $nights
        + $order->per_child_rate * $children * $nights;

    $extraLines = $extraLines ?? [];
    $extrasTotal = $order->extras_total ?? 0;
    $subtotal = $accommodation + $extrasTotal;
    $discountTotal = ($order->last_minute_discount ?? 0) + ($order->coupon_discount ?? 0);

    $hasLastMinute = $order->last_minute_applied && $order->last_minute_discount;
    $hasCoupon = (bool) $order->coupon_discount;
    $hasDiscount = $discountTotal > 0;
@endphp

<dl class="space-y-2 text-sm text-black">
    <div class="space-y-2">
        <div class="flex justify-between">
            <dt>Plek ({{ $order->season_name }}) · {{ $euro($order->nightly_rate) }} × {{ $order->num_nights }} {{ $order->num_nights === 1 ? 'nacht' : 'nachten' }}</dt>
            <dd class="font-medium text-black">{{ $euro($order->nightly_rate * $order->num_nights) }}</dd>
        </div>

        <div class="flex justify-between">
            <dt>Volwassenen · {{ $euro($order->per_adult_rate) }} × {{ $adults }} × {{ $order->num_nights }}</dt>
            <dd class="font-medium text-black">{{ $euro($order->per_adult_rate * $adults * $order->num_nights) }}</dd>
        </div>

        @if ($children > 0)
            <div class="flex justify-between">
                <dt>Kinderen · {{ $euro($order->per_child_rate) }} × {{ $children }} × {{ $order->num_nights }}</dt>
                <dd class="font-medium text-black">{{ $euro($order->per_child_rate * $children * $order->num_nights) }}</dd>
            </div>
        @endif
    </div>

    {{-- Extras, one line per booked type --}}
    @if (! empty($extraLines))
        <div class="space-y-2 border-t border-tan-500 pt-2">
            @foreach ($extraLines as $line)
                <div class="flex justify-between">
                    <dt>{{ $line['name'] }} · {{ $line['quantity'] }}× {{ $line['per_night'] ? 'per nacht' : 'eenmalig' }}</dt>
                    <dd class="font-medium text-black">{{ $euro($line['subtotal']) }}</dd>
                </div>
            @endforeach
        </div>
    @elseif ($extrasTotal > 0)
        <div class="flex justify-between border-t border-tan-500 pt-2">
            <dt>Extra's</dt>
            <dd class="font-medium text-black">{{ $euro($extrasTotal) }}</dd>
        </div>
    @endif

    <div class="flex justify-between border-t border-tan-500 pt-2">
        <dt class="font-semibold text-black">Subtotaal</dt>
        <dd class="font-semibold text-black">{{ $euro($subtotal) }}</dd>
    </div>

    {{-- Discounts: only shown when something is actually deducted --}}
    @if ($hasDiscount)
        <div class="space-y-2 border-t border-tan-500 pt-2">
            @if ($hasLastMinute)
                <div class="flex justify-between text-olivegreen-700">
                    <dt>Last-minute korting</dt>
                    <dd>− {{ $euro($order->last_minute_discount) }}</dd>
                </div>
            @endif

            @if ($hasCoupon)
                <div class="flex justify-between text-olivegreen-700">
                    <dt>Couponkorting</dt>
                    <dd>− {{ $euro($order->coupon_discount) }}</dd>
                </div>
            @endif
        </div>
    @endif

    {{-- Total --}}
    <div class="flex justify-between border-t-2 border-tan-500 pt-2 text-base">
        <dt class="font-semibold text-black">Totaal</dt>
        <dd class="font-bold text-black">{{ $euro($order->total) }}</dd>
    </div>
</dl>
