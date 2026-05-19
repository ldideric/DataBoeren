<?php

namespace App\Http\Controllers;

use App\Models\Campsite;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CampsiteController extends Controller
{
    public function index(Request $request): View
    {
        $checkIn = $request->date('datestart');
        $checkOut = $request->date('dateend');

        $hasRange = $checkIn && $checkOut && $checkOut->greaterThan($checkIn);

        $campsites = Campsite::query()
            ->when($hasRange, fn ($q) => $q->whereAvailableBetween($checkIn, $checkOut))
            ->orderBy('name')
            ->get();

        return view('campsites.index', [
            'campsites' => $campsites,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
        ]);
    }
}
