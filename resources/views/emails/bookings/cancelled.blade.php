<x-mail::message>
# Reservering geannuleerd

Beste {{ $reservation->customer->first_name }},

Uw reservering bij Camping De Groene Weide is geannuleerd.

- **Plek:** {{ $reservation->campsite->name }}
- **Aankomst:** {{ $reservation->check_in->format('d M Y') }}
- **Vertrek:** {{ $reservation->check_out->format('d M Y') }}
- **Referentie:** {{ $reservation->id }}

Heeft u dit niet zelf gedaan? Neem dan contact met ons op.

Groet,
{{ config('app.name') }}
</x-mail::message>
