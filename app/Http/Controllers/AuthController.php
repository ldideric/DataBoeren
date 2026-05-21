<?php

namespace App\Http\Controllers;

use App\Actions\SendBookingsLink;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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

    public function sendLink(Request $request, SendBookingsLink $sendBookingsLink): View
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()
            ->whereCustomer()
            ->where('email', $data['email'])
            ->first();

        $user && $sendBookingsLink->handle($user);

        return view('auth.link-sent');
    }
}
