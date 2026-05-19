# TODO

## Database
- Add address fields to `users` (city, street, postal_code, country).
- Add vehicle fields to `reservations` (num_plate, voertuigtype).

## Payments
- Add stub payment page at `/bookings/{reservation}/payment` (Confirm/Cancel).
- Persist `pay_method` on a Payment row.

## Booking flow
- Add confirmation page + email after `store()`.
- Move `destroy` auth check into a `ReservationPolicy`.

## Frontend
- Add `old(...)` defaults to form fields.
- Add active-link styling in the nav.
