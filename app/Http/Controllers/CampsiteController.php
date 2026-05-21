<?php

namespace App\Http\Controllers;

use App\Models\Campsite;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CampsiteController extends Controller
{
    public function index(Request $request): View
    {
        $checkIn = $request->date('datestart');
        $checkOut = $request->date('dateend');
        $adults = $request->filled('adults') ? max(1, (int) $request->integer('adults')) : null;
        $children = $request->filled('children') ? max(0, (int) $request->integer('children')) : null;
        $vehicles = $request->filled('vehicles') ? max(0, (int) $request->integer('vehicles')) : null;

        $hasValidDates = $checkIn
            && $checkOut
            && $checkOut->greaterThan($checkIn)
            && $checkIn->greaterThanOrEqualTo(Carbon::today());

        $hasAllCriteria = $hasValidDates
            && $adults !== null
            && $children !== null
            && $vehicles !== null;

        $campsites = $hasAllCriteria
            ? Campsite::query()
                ->whereFitsParty($adults + $children, $vehicles)
                ->whereAvailableBetween($checkIn, $checkOut)
                ->orderBy('name')
                ->get()
            : new Collection();

        return view('campsites.index', [
            'campsites' => $campsites,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'adults' => $adults,
            'children' => $children,
            'vehicles' => $vehicles,
            'hasAllCriteria' => $hasAllCriteria,
        ]);
    }
}
