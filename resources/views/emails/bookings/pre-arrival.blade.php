<x-mail::message>
# Tot binnenkort!

Beste {{ $reservation->customer->first_name }},

Uw verblijf bij Camping De Groene Weide komt eraan. We kijken ernaar uit u te ontvangen!

- **Plek:** {{ $reservation->campsite->name }}
- **Aankomst:** {{ $reservation->check_in->format('d M Y') }}
- **Vertrek:** {{ $reservation->check_out->format('d M Y') }}
- **Referentie:** {{ $reservation->id }}

**Goed om te weten:** inchecken kan vanaf 14:00 uur. Houd uw reserveringsnummer bij de hand bij aankomst.

Tot snel op de camping!

Groet,
{{ config('app.name') }}
</x-mail::message>
