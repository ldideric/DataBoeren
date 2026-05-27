<x-mail::message>
# Reservering bevestigd

Beste {{ $reservation->customer->first_name }},

Uw reservering bij Camping De Groene Weide is bevestigd.

- **Plek:** {{ $reservation->campsite->name }}
- **Aankomst:** {{ $reservation->check_in->format('d M Y') }}
- **Vertrek:** {{ $reservation->check_out->format('d M Y') }}
- **Aantal volwassenen:** {{ $reservation->num_adults }}
- **Aantal kinderen:** {{ $reservation->num_children }}
- **Referentie:** {{ $reservation->id }}

Tot ziens op de camping!

Groet,
{{ config('app.name') }}
</x-mail::message>
