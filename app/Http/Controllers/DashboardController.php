<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** The first screen after signing in. */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('dashboard', [
            'user' => $request->user(),
            'units' => Unit::with('station')->orderBy('name')->get(),
        ]);
    }
}
