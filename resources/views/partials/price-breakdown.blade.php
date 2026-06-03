@php
    // Amounts are integer cents; format to euros only at display time.
    $euro = fn ($cents) => '€ ' . number_format($cents / 100, 2, ',', '.');
@endphp

<dl class="space-y-2 text-sm text-black">
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

    @if ($order->last_minute_applied && $order->last_minute_discount)
        <div class="flex justify-between pt-3 font-semibold text-black">
            <dt>Last-minute korting</dt>
            <dd class="font-medium">− {{ $euro($order->last_minute_discount) }}</dd>
        </div>
    @endif

    @if ($order->coupon_discount)
        <div class="flex justify-between font-semibold text-black">
            <dt>Couponkorting</dt>
            <dd class="font-medium">− {{ $euro($order->coupon_discount) }}</dd>
        </div>
    @endif

    @if ($order->extras_total > 0)
        <div class="flex justify-between">
            <dt>Extra's</dt>
            <dd class="font-medium text-black">{{ $euro($order->extras_total) }}</dd>
        </div>
    @endif

    <div class="flex justify-between border-t border-tan-500 pt-2 text-base">
        <dt class="font-semibold text-black">Totaal</dt>
        <dd class="font-bold text-black">{{ $euro($order->total) }}</dd>
    </div>
</dl>
