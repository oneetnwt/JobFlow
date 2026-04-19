<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\View\View;

class PricingController extends Controller
{
    /**
     * Show the public pricing page.
     */
    public function index(): View
    {
        $plans = Plan::active()->orderBy('monthly_price')->get();

        return view('central.pricing', compact('plans'));
    }
}
