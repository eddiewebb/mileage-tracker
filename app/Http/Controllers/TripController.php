<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Label;
use App\Models\MileageRate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TripController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of trips.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $user->trips()->with('labels');

        // Text search filter
        if ($request->has('search') && $request->search) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('start_location', 'like', "%{$search}%")
                  ->orWhere('end_location', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->has('start_date') && $request->start_date) {
            $query->where('trip_date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->where('trip_date', '<=', $request->end_date);
        }

        // Tag filter
        if ($request->has('tags') && is_array($request->tags) && !empty($request->tags)) {
            $query->whereHas('labels', function($q) use ($request) {
                $q->whereIn('name', $request->tags);
            });
        }

        $trips = $query->orderBy('trip_date', 'desc')
                      ->orderBy('trip_time', 'desc')
                      ->paginate(20)
                      ->appends($request->query());

        // Get all user labels for filter dropdown
        $userLabels = $user->labels()->orderBy('name')->get();

        return view('trips.index', compact('trips', 'userLabels'));
    }

    /**
     * Show the form for creating a new trip.
     */
    public function create()
    {
        $labels = auth()->user()->labels()->orderBy('name')->get();
        return view('trips.create', compact('labels'));
    }

    /**
     * Store a newly created trip.
     */
    public function store(Request $request)
    {
        $this->validateTripData($request);

        $trip = $this->createTrip($request);
        $this->attachLabelsToTrip($trip, $request);

        return redirect()->route('trips.index')
            ->with('success', 'Trip created successfully!');
    }

    /**
     * Display the specified trip.
     */
    public function show(Trip $trip)
    {
        $this->authorizeTrip($trip);
        
        return view('trips.show', compact('trip'));
    }

    /**
     * Show the form for editing the specified trip.
     */
    public function edit(Trip $trip)
    {
        $this->authorizeTrip($trip);
        
        $labels = auth()->user()->labels()->orderBy('name')->get();
        
        return view('trips.edit', compact('trip', 'labels'));
    }

    /**
     * Update the specified trip.
     */
    public function update(Request $request, Trip $trip)
    {
        $this->authorizeTrip($trip);
        $this->validateTripData($request);

        $this->updateTrip($trip, $request);
        $this->syncLabelsToTrip($trip, $request);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Trip updated successfully!');
    }

    /**
     * Remove the specified trip.
     */
    public function destroy(Trip $trip)
    {
        $this->authorizeTrip($trip);
        
        $trip->delete();

        return redirect()->route('trips.index')
            ->with('success', 'Trip deleted successfully!');
    }

    /**
     * Validate trip data.
     */
    private function validateTripData(Request $request)
    {
        $request->validate([
            'start_location' => ['required', 'string', 'max:255'],
            'end_location' => ['required', 'string', 'max:255'],
            'mileage' => ['required', 'numeric', 'min:0.01'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'trip_date' => ['required', 'date'],
            'trip_time' => ['required', 'date_format:H:i'],
            'labels' => ['nullable', 'array'],
            'labels.*' => ['string', 'max:50'],
            'start_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'start_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'end_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'end_longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);
    }

    /**
     * Create a new trip.
     */
    private function createTrip(Request $request)
    {
        return auth()->user()->trips()->create([
            'start_location' => $request->start_location,
            'end_location' => $request->end_location,
            'mileage' => $request->mileage,
            'purpose' => $request->purpose,
            'notes' => $request->notes,
            'trip_date' => $request->trip_date,
            'trip_time' => Carbon::createFromFormat('H:i', $request->trip_time),
            'start_latitude' => $request->start_latitude,
            'start_longitude' => $request->start_longitude,
            'end_latitude' => $request->end_latitude,
            'end_longitude' => $request->end_longitude,
        ]);
    }

    /**
     * Update an existing trip.
     */
    private function updateTrip(Trip $trip, Request $request)
    {
        $trip->update([
            'start_location' => $request->start_location,
            'end_location' => $request->end_location,
            'mileage' => $request->mileage,
            'purpose' => $request->purpose,
            'notes' => $request->notes,
            'trip_date' => $request->trip_date,
            'trip_time' => Carbon::createFromFormat('H:i', $request->trip_time),
            'start_latitude' => $request->start_latitude,
            'start_longitude' => $request->start_longitude,
            'end_latitude' => $request->end_latitude,
            'end_longitude' => $request->end_longitude,
        ]);
    }

    /**
     * Attach labels to a trip.
     */
    private function attachLabelsToTrip(Trip $trip, Request $request)
    {
        if (!$request->has('labels')) {
            return;
        }

        $labelIds = [];
        foreach ($request->labels as $labelName) {
            $label = Label::findOrCreateForUser(auth()->id(), $labelName);
            $labelIds[] = $label->id;
        }

        $trip->labels()->attach($labelIds);
    }

    /**
     * Sync labels to a trip.
     */
    private function syncLabelsToTrip(Trip $trip, Request $request)
    {
        if (!$request->has('labels')) {
            $trip->labels()->detach();
            return;
        }

        $labelIds = [];
        foreach ($request->labels as $labelName) {
            $label = Label::findOrCreateForUser(auth()->id(), $labelName);
            $labelIds[] = $label->id;
        }

        $trip->labels()->sync($labelIds);
    }

    /**
     * Authorize trip access.
     */
    private function authorizeTrip(Trip $trip)
    {
        if ($trip->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to trip.');
        }
    }
}
