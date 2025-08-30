<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Label;
use App\Models\MileageRate;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        
        $stats = $this->calculateUserStats($user);
        $recentTrips = $this->getRecentTrips($user);
        $currentYearRate = $this->getCurrentYearMileageRate($user);

        return view('dashboard', compact('stats', 'recentTrips', 'currentYearRate'));
    }

    /**
     * Calculate user statistics.
     */
    private function calculateUserStats($user)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        return [
            'total_trips' => $user->trips()->count(),
            'current_year_trips' => $user->trips()->whereYear('trip_date', $currentYear)->count(),
            'current_month_trips' => $user->trips()
                ->whereYear('trip_date', $currentYear)
                ->whereMonth('trip_date', $currentMonth)
                ->count(),
            'current_year_miles' => $user->trips()
                ->whereYear('trip_date', $currentYear)
                ->sum('mileage'),
            'current_month_miles' => $user->trips()
                ->whereYear('trip_date', $currentYear)
                ->whereMonth('trip_date', $currentMonth)
                ->sum('mileage'),
        ];
    }

    /**
     * Get the user's recent trips.
     */
    private function getRecentTrips($user)
    {
        return $user->trips()
            ->with('labels')
            ->orderBy('trip_date', 'desc')
            ->orderBy('trip_time', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get or create the current year mileage rate.
     */
    private function getCurrentYearMileageRate($user)
    {
        $currentYear = Carbon::now()->year;
        return MileageRate::getOrCreateForUserAndYear($user->id, $currentYear);
    }
}
