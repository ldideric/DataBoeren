<x-mail::message>
# We hebben uw reservering ontvangen

Beste {{ $reservation->customer->first_name }},

Bedankt voor uw reservering bij Camping De Groene Weide. We hebben uw boeking ontvangen.

- **Plek:** {{ $reservation->campsite->name }}
- **Aankomst:** {{ $reservation->check_in->format('d M Y') }}
- **Vertrek:** {{ $reservation->check_out->format('d M Y') }}
- **Aantal volwassenen:** {{ $reservation->num_adults }}
- **Aantal kinderen:** {{ $reservation->num_children }}
- **Referentie:** {{ $reservation->id }}
@if ($reservation->orderSummary)
- **Te betalen op locatie:** € {{ number_format($reservation->orderSummary->total / 100, 2, ',', '.') }}
@endif

<x-mail::button :url="$bookingsUrl">
Bekijk mijn reservering
</x-mail::button>

Deze link is **60 minuten** geldig. Heeft u een nieuwe link nodig? Vraag deze opnieuw aan via 'mijn boekingen'de website.

Tot ziens op de camping!

Vriendelijke groet,
{{ config('app.name') }}
</x-mail::message>
