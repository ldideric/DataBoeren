<x-mail::message>
# Uw boekingen

Beste {{ $user->first_name }},

Klik op de knop hieronder om uw boekingen bij Camping De Groene Weide te bekijken of te annuleren.

<x-mail::button :url="$signedUrl">
Bekijk mijn boekingen
</x-mail::button>

Deze link is **60 minuten** geldig. Heeft u deze link niet aangevraagd? Dan kunt u deze e-mail negeren.

Groet,
{{ config('app.name') }}
</x-mail::message>
