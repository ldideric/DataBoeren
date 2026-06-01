# TODO

## Payments
- Add stub payment page at `/bookings/{reservation}/payment` (Confirm/Cancel).
- Persist `pay_method` on a Payment row.
- Add toeristenbelasting (tourist tax) to the invoices at a later stage.

## Booking flow
- Add confirmation page + email after `store()`.
- Persist `huisregels` / `adult_confirmation` acceptance (audit trail — currently required at submit but discarded).

## Email (Mailgun)
**Status:** infra-ready, app not wired. The deployment already injects `MAILGUN_DOMAIN`,
`MAILGUN_SECRET`, and `MAILGUN_ENDPOINT` into the prod + staging containers (shared
Mailgun **sandbox** creds). `MAIL_MAILER` is still `log`, so mail goes to the log, not out.

- [ ] `composer require symfony/mailgun-mailer symfony/http-client` (commit `composer.json` + `composer.lock` — the prod image builds via `composer install` from the lock).
- [ ] Add a `mailgun` block to `config/services.php`:
  ```php
  'mailgun' => [
      'domain'   => env('MAILGUN_DOMAIN'),
      'secret'   => env('MAILGUN_SECRET'),
      'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
      'scheme'   => 'https',
  ],
  ```
- [ ] Add `'mailgun' => ['transport' => 'mailgun'],` to `mailers` in `config/mail.php`.
- [ ] Flip `MAIL_MAILER` to `mailgun` — prod/staging in the deployment repo's `docker-compose.yml` (both de-groene-weide services); locally optional (keep Mailpit/`log` for dev).
- [ ] Document `MAILGUN_DOMAIN` / `MAILGUN_SECRET` / `MAILGUN_ENDPOINT` in this app's `.env.example`.
- [ ] Rebuild & push `ghcr.io/ldideric/de-groene-weide` so prod picks up the new dep + config (local dev gets it via the volume mount).
- [ ] Test-send to an authorized sandbox recipient (sandbox only delivers to whitelisted addresses).

Mapping (Mailgun dashboard → env var Laravel reads): sandbox domain → `MAILGUN_DOMAIN`,
API key → `MAILGUN_SECRET`, base URL `https://api.mailgun.net` → `MAILGUN_ENDPOINT`
(host only, no scheme). Sandbox → real domain later: swap the values in the server `.env` and redeploy.

## Optional Filament features

These are good additions once the basics are stable, ordered roughly by business value:

1. **ReservationResource — Confirm action** — sets `status = Confirmed`, optionally emails customer. Guard: only visible when `status === Pending`.
2. **ReservationResource — Cancel action** — modal with required reason field; sets `cancelled_at`, `cancelled_by_user_id`, `cancellation_reason`. Guard: not visible when already cancelled.
3. **ReservationResource — Mark paid manually** — creates `Payment` record. Guard: only when no `Paid` payment exists yet.
4. **ReservationResource — Send payment link** — fires notification email with Stripe checkout URL.
5. **CustomerResource — Purge data (GDPR)** — confirmation modal, sets `purged_at`, nullifies PII. The column already exists.
6. **CustomerResource — Send magic link** — dispatches the magic-link email directly from the panel.
7. **CouponResource — Expire now** — sets `expires_at = now()`. Quick deactivation without deleting.
8. **ExtraResource — Adjust stock** — modal with `+/-` input added to current `stock`. Safer than direct numeric edit.