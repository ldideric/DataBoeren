<x-mail::message>
# Rond uw reservering af

Beste {{ $reservation->customer->first_name }},

Uw reservering bij Camping De Groene Weide staat nog open. We hebben uw betaling nog niet ontvangen, waardoor uw boeking nog **niet bevestigd** is.

- **Plek:** {{ $reservation->campsite->name }}
- **Aankomst:** {{ $reservation->check_in->format('d M Y') }}
- **Vertrek:** {{ $reservation->check_out->format('d M Y') }}
@if ($reservation->orderSummary)
- **Te betalen:** € {{ number_format($reservation->orderSummary->total / 100, 2, ',', '.') }}
@endif

<x-mail::button :url="$paymentUrl">
Nu betalen
</x-mail::button>

Deze betaallink is **60 minuten** geldig. Heeft u inmiddels al betaald? Dan kunt u deze e-mail negeren.

Vriendelijke groet,
{{ config('app.name') }}
</x-mail::message>
