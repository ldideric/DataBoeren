<?php

use App\Auth\Services\SignedUrlGenerator;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Contracts\Routing\UrlGenerator;

afterEach(fn () => Mockery::close());

test('signed url generator delegates to the url generator', function () {
    $url = Mockery::mock(UrlGenerator::class);
    $url->shouldReceive('temporarySignedRoute')
        ->andReturnUsing(fn ($name, $expiration, $parameters) => "signed://{$name}/".implode('-', (array) $parameters));

    $generator = new SignedUrlGenerator($url);

    $user = new User;
    $user->id = 7;
    $reservation = new Reservation;
    $reservation->id = 13;

    expect($generator->bookings($user))->toBe('signed://bookings.index/7')
        ->and($generator->cancelReservation($user, $reservation))->toBe('signed://bookings.destroy/7-13')
        ->and($generator->payment($reservation))->toBe('signed://payments.show/13')
        ->and($generator->checkout($reservation))->toBe('signed://payments.checkout/13');
});
