# TODO

## Payments
- Add toeristenbelasting (tourist tax) to the invoices at a later stage.

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