# TODO

## Payments
- Add stub payment page at `/bookings/{reservation}/payment` (Confirm/Cancel).
- Persist `pay_method` on a Payment row.

## Booking flow
- Add confirmation page + email after `store()`.
- Move `destroy` auth check into a `ReservationPolicy`.
- Persist `huisregels` / `adult_confirmation` acceptance (audit trail — currently required at submit but discarded).
- Store adults/children breakdown separately on reservations (currently summed into `num_people`, losing the split).

## Frontend
- Add active-link styling in the nav.
