@php
    $euro = fn ($amount) => '€ ' . number_format((float) $amount, 2, ',', '.');
@endphp

<dl class="space-y-2 text-sm text-gray-700">
    <div class="flex justify-between">
        <dt>Plek ({{ $order->season_name }}) · {{ $euro($order->nightly_rate) }} × {{ $order->num_nights }} {{ $order->num_nights === 1 ? 'nacht' : 'nachten' }}</dt>
        <dd class="font-medium text-gray-900">{{ $euro($order->nightly_rate * $order->num_nights) }}</dd>
    </div>

    <div class="flex justify-between">
        <dt>Volwassenen · {{ $euro($order->per_adult_rate) }} × {{ $adults }} × {{ $order->num_nights }}</dt>
        <dd class="font-medium text-gray-900">{{ $euro($order->per_adult_rate * $adults * $order->num_nights) }}</dd>
    </div>

    @if ($children > 0)
        <div class="flex justify-between">
            <dt>Kinderen · {{ $euro($order->per_child_rate) }} × {{ $children }} × {{ $order->num_nights }}</dt>
            <dd class="font-medium text-gray-900">{{ $euro($order->per_child_rate * $children * $order->num_nights) }}</dd>
        </div>
    @endif

    @if ($order->last_minute_applied && $order->last_minute_discount)
        <div class="flex justify-between text-green-700">
            <dt>Last-minute korting</dt>
            <dd class="font-medium">− {{ $euro($order->last_minute_discount) }}</dd>
        </div>
    @endif

    @if ($order->coupon_discount)
        <div class="flex justify-between text-green-700">
            <dt>Couponkorting</dt>
            <dd class="font-medium">− {{ $euro($order->coupon_discount) }}</dd>
        </div>
    @endif

    @if ($order->extras_total > 0)
        <div class="flex justify-between">
            <dt>Extra's</dt>
            <dd class="font-medium text-gray-900">{{ $euro($order->extras_total) }}</dd>
        </div>
    @endif

    <div class="flex justify-between border-t border-gray-200 pt-2 text-base">
        <dt class="font-semibold text-gray-900">Totaal</dt>
        <dd class="font-bold text-gray-900" data-grand-total data-base="{{ $order->total }}" data-nights="{{ $order->num_nights }}">{{ $euro($order->total) }}</dd>
    </div>
</dl>
