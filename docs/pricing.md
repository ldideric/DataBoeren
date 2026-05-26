# Pricing & discounts

How a reservation turns into a price, and the design decisions behind it. This
documents the data model in `campsite_prices`, `extras`, `coupons` and
`order_summaries`, plus the scaffolding in `app/Actions/CalculatePrice.php` and
`app/Support/ExtraAvailability.php`.

## Mental model: rate card + frozen snapshot

There are two halves to pricing:

1. **The live rate card** — `campsite_prices` holds, per `(campsite, season)`, a
   `nightly_rate`, a `per_adult_rate` and a `per_child_rate`. Admins edit these.
2. **The frozen invoice** — `order_summaries` is a one-to-one snapshot per
   reservation. At booking time we *copy* the resolved rates and season name
   into it. Once written it is never recomputed, so editing the rate card later
   never rewrites the price of an existing booking.

`CalculatePrice` reads the rate card and produces an `OrderSummary`.

## The pipeline

```
1. season      → the Season whose [starts_at, ends_at] contains check_in
2. rate card   → campsite_prices row for (campsite, season)
3. num_nights  → check_out − check_in

4. accommodation = (nightly_rate
                    + per_adult_rate × num_adults
                    + per_child_rate × num_children) × num_nights

5. − last_minute_discount     (config rule, on accommodation)
6. − coupon_discount          (accommodation- or extra-scoped)
7. + extras_total             (Σ extra line subtotals)

= total
```

Discounts are layered in this order: **last-minute first, then coupon.** A
coupon that is percentage-based and accommodation-scoped therefore applies to
the already-last-minute-reduced accommodation subtotal.

---

## Q1 — Seasons / multi-season stays

**Decision: the whole stay is priced in the season that contains the check-in
date.** No multi-season splitting.

No schema change. `CalculatePrice::seasonFor()` looks up the single season
covering `check_in`. The single `season_name` / rate columns on `order_summaries`
already match this.

⚠️ This relies on the configured seasons fully covering the calendar with **no
gaps and no overlaps** — otherwise a check-in date resolves to zero or two
seasons. `seasonFor()` throws if no season matches; consider enforcing coverage
when seasons are managed in the admin.

## Q2 — Separate child price

**Decision: add `per_child_rate` alongside the adult rate.**

`per_person_rate` was renamed to **`per_adult_rate`** (it only ever charged
adults) and **`per_child_rate`** added — on both `campsite_prices` and the
`order_summaries` snapshot. This mirrors the existing `num_people → num_adults` +
`num_children` split on reservations. Children are now billed at their own
per-night rate; set `per_child_rate = 0` to make children free for a campsite.

## Q3 — Extras: rentals & inventory

You were right that the old shape didn't fit. Two separate concerns were tangled
together, so we split them and dropped what didn't belong:

| Concern | Before | Now |
|---|---|---|
| "Is this offered at all?" | `available` boolean | **dropped** — anyone can book any extra; use SoftDeletes to retire one |
| "How is it priced?" | `billing_type` incl. confusing `rental` | `billing_type` = `one_time` or `per_night` only |
| "How many exist to rent?" | (none) | **`stock`** — total concurrent units; `NULL` = unlimited |
| "How many per booking?" | (none) | **`max_per_booking`** — per-reservation cap; `NULL` = no cap |

This maps directly to your two examples:

- **Dog** — anyone can bring one, but at most a few per booking:
  `stock = NULL` (unlimited overall), `max_per_booking = 3`.
- **Firepit** — only 5 exist in the whole park:
  `stock = 5`, `max_per_booking` = `NULL` or `1`.

`rental` left the enum because "it's a rental" was really "it has limited
inventory" — now captured by `stock`, independent of how it's billed.

### How the "max 5 firepits on any day" check works

Limited stock is a small pool of identical units checked out for a date range
and returned. The rule is **per night**: on no single night may units-in-use
exceed `stock`. The interesting bit is that a naive "sum everything overlapping
the requested range" over-counts — two bookings that both overlap your window
but not *each other* never compete for a unit. So we compute the **peak
concurrent usage** across the requested nights instead.

`App\Support\ExtraAvailability` does this:

```php
$remaining = ExtraAvailability::for($firepit)->remaining($checkIn, $checkOut);
// null  → unlimited
// 0..5  → units still rentable on the tightest night

ExtraAvailability::for($firepit)->canRent(2, $checkIn, $checkOut); // bool
```

It loads `reservation_extras` for that extra whose reservation is
pending/confirmed and overlaps the window, then walks night-by-night summing
quantities and takes the max. Stays are short so the per-night loop is cheap;
for longer horizons you'd sweep booking boundaries instead.

⚠️ **Concurrency:** the read is correct but not race-safe on its own. When wired
into booking, run it inside the same locked transaction as `CreateReservation`
(see its `lockForUpdate` note) so two requests can't both grab the last firepit.
`max_per_booking` is a plain validation rule on the quantity field — not part of
the availability calculation.

## Q4 — Last-minute discount

**Decision: a single global rule in `config/pricing.php`, not a table.**

```php
'last_minute' => ['enabled' => true, 'threshold_days' => 7, 'discount_percent' => 10],
```

There's no per-row data to model — it's one business rule — so config is the
right home. If `check_in` is within `threshold_days` of today, a
`discount_percent` reduction is taken off the **accommodation** subtotal.

The *outcome* is still snapshotted onto `order_summaries.last_minute_applied` /
`last_minute_discount`, so changing the config never alters past bookings.
`CalculatePrice::lastMinuteDiscount()` implements it.

## Q5 — Coupons: accommodation discounts *and* free extras

**Decision: give a coupon a `scope` plus an optional target extra.**

New columns on `coupons`:

- **`scope`** (`CouponScope`: `accommodation` | `extra`) — what the discount hits.
- **`extra_id`** (nullable FK) — which extra a `scope = extra` coupon targets.

`discount_type` (`flat` | `percent`) + `discount_value` are unchanged. The
combinations cover both cases you described:

| Goal | scope | discount_type | discount_value | extra_id |
|---|---|---|---|---|
| 10% off accommodation | `accommodation` | `percent` | `10` | — |
| €15 off accommodation | `accommodation` | `flat` | `15` | — |
| Free firepit | `extra` | `percent` | `100` | firepit |
| €5 off the dog fee | `extra` | `flat` | `5` | dog |

The `order_summaries.coupon_discount` column holds the resulting **monetary
value** regardless of scope, so no snapshot change was needed. An extra-scoped
coupon only discounts its target **if the guest actually selected that extra** —
it discounts an existing line rather than auto-adding one (auto-adding can be a
later UX choice). `CalculatePrice::couponDiscount()` implements this.

> Note: `Coupon::max_uses` / `uses_count` already gate validity; incrementing
> `uses_count` on a successful booking is a `@todo` in `CalculatePrice`.

---

## Schema changes applied

| Migration | Change |
|---|---|
| `..._add_per_child_rate_to_campsite_prices` | rename `per_person_rate`→`per_adult_rate`, add `per_child_rate` |
| `..._add_per_child_rate_to_order_summaries` | same rename + add on the snapshot |
| `..._replace_available_with_stock_on_extras` | drop `available`; add `stock`, `max_per_booking` |
| `..._add_scope_and_extra_to_coupons` | add `scope`, `extra_id` |

Enums: `BillingType` lost `Rental`; new `CouponScope`.

## Scaffolding & next steps

Starting points, not finished code:

- `app/Actions/CalculatePrice.php` — reservation (+ chosen extras) → unsaved
  `OrderSummary`. Caller persists it with the `reservation_extras` lines in one
  transaction.
- `app/Support/ExtraAvailability.php` — inventory check for limited-stock extras.
- `config/pricing.php` — last-minute rule.

Still to wire up:

1. Persist the `OrderSummary` + `reservation_extras` rows (and increment
   `Coupon::uses_count`) inside `CreateReservation`'s transaction.
2. Validate `quantity ≤ max_per_booking` and `ExtraAvailability::canRent(...)`
   in `BookingRequest`, under the campsite lock.
3. Unit-test `CalculatePrice` (rate lookup, child rate, last-minute window,
   each coupon scope) and `ExtraAvailability` (the peak-vs-sum edge case).
4. A Filament `ExtraResource` exposing `stock` / `max_per_booking`.
