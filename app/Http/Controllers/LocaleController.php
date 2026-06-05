<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetPanelLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, SetPanelLocale::SUPPORTED, true)) {
            $locale = 'nl';
        }

        $request->session()->put('locale', $locale);

        if ($user = $request->user()) {
            $user->update(['locale' => $locale]);
        }

        return back();
    }
}
