<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Mail\MagicLink;
use App\Models\User;
use App\Support\SignedLink;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
            Mail::to($user->email)->send(new MagicLink($user, SignedLink::bookings($user)));
        }

        return view('auth.link-sent');
    }
}
