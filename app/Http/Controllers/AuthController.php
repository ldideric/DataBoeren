<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Mail\MagicLink;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{
    public function requestForm(): View
    {
        return view('auth.request');
    }

    public function linkSent(): View
    {
        return view('auth.link-sent');
    }

    public function sendLink(Request $request): View
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()
            ->where('email', $data['email'])
            ->where('role', UserRole::Customer)
            ->first();

        if ($user) {
            $url = URL::temporarySignedRoute(
                'login.verify',
                now()->addMinutes(15),
                ['user' => $user->id],
            );

            Mail::to($user->email)->send(new MagicLink($user, $url));
        }

        return view('auth.link-sent');
    }

    public function verify(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role === UserRole::Customer, 404);

        Auth::login($user);

        if ($user->email_verified_at === null) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $request->session()->regenerate();

        return redirect()->intended(route('bookings.index'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
