<x-mail::message>
# Inloglink

Beste {{ $user->first_name }},

U vroeg een inloglink aan voor uw account bij Camping De Groene Weide. Klik op de knop hieronder om in te loggen.

<x-mail::button :url="$signedUrl">
Inloggen
</x-mail::button>

Deze link is **15 minuten** geldig. Heeft u geen inloglink aangevraagd? Dan kunt u deze e-mail negeren.

Groet,
{{ config('app.name') }}
</x-mail::message>
