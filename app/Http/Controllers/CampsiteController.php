<?php

namespace App\Http\Controllers;

use App\Booking\DTO\StayCriteria;
use App\Models\Campsite;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CampsiteController extends Controller
{
    public function index(Request $request): View
    {
        $criteria = StayCriteria::fromRequest($request);

        $campsites = $criteria->isComplete()
            ? Campsite::query()
                ->whereFitsParty($criteria->partySize(), $criteria->vehicles)
                ->whereAvailableBetween($criteria->checkIn, $criteria->checkOut)
                ->orderBy('name')
                ->paginate(8)
                ->withQueryString()
            : new Collection();

        return view('campsites.index', [
            'campsites' => $campsites,
            'checkIn' => $criteria->checkIn,
            'checkOut' => $criteria->checkOut,
            'adults' => $criteria->adults,
            'children' => $criteria->children,
            'vehicles' => $criteria->vehicles,
            'hasAllCriteria' => $criteria->isComplete(),
        ]);
    }
}
