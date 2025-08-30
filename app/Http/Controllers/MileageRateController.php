<?php

namespace App\Http\Controllers;

use App\Models\MileageRate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MileageRateController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the mileage rates management page.
     */
    public function index()
    {
        $user = auth()->user();
        $rates = $this->getUserMileageRates($user);
        
        return view('mileage-rates.index', compact('rates'));
    }

    /**
     * Update the mileage rate for a specific year.
     */
    public function update(Request $request)
    {
        $this->validateMileageRateRequest($request);

        $rate = $this->updateOrCreateMileageRate($request);

        return response()->json([
            'success' => true,
            'message' => "Mileage rate for {$request->year} updated successfully!",
            'rate' => $rate,
        ]);
    }

    /**
     * Get all mileage rates for the authenticated user.
     */
    private function getUserMileageRates($user)
    {
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 5, $currentYear + 1); // Show 5 years back and 1 year forward
        
        $rates = [];
        foreach ($years as $year) {
            $rate = $user->mileageRates()->where('year', $year)->first();
            if (!$rate) {
                $rate = MileageRate::getOrCreateForUserAndYear($user->id, $year);
            }
            $rates[] = $rate;
        }

        return collect($rates)->sortByDesc('year');
    }

    /**
     * Validate mileage rate request.
     */
    private function validateMileageRateRequest(Request $request)
    {
        $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:' . (Carbon::now()->year + 10)],
            'rate_cents_per_mile' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);
    }

    /**
     * Update or create mileage rate.
     */
    private function updateOrCreateMileageRate(Request $request)
    {
        return MileageRate::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'year' => $request->year,
            ],
            [
                'rate_cents_per_mile' => $request->rate_cents_per_mile,
            ]
        );
    }
}
