<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class CampsiteController extends Controller
{
    public function index(): View
    {
        return view('campsites.index');
    }
}
