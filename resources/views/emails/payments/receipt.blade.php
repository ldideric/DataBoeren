<x-mail::message>
# Betaling ontvangen

Beste {{ $reservation->customer->first_name }},

We hebben uw betaling goed ontvangen. Bewaar deze e-mail als betalingsbewijs.

- **Plek:** {{ $reservation->campsite->name }}
- **Aankomst:** {{ $reservation->check_in->format('d M Y') }}
- **Vertrek:** {{ $reservation->check_out->format('d M Y') }}
- **Betaald bedrag:** € {{ number_format($payment->amount / 100, 2, ',', '.') }}
- **Betaalmethode:** {{ $payment->method->getLabel() }}
- **Betaald op:** {{ optional($payment->paid_at)->format('d M Y H:i') ?? '—' }}
- **Referentie:** {{ $reservation->id }}

Tot ziens op de camping!

Vriendelijke groet,
{{ config('app.name') }}
</x-mail::message>
