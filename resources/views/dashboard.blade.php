@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Dashboard</h1>
            <p class="text-muted">Welcome back, {{ Auth::user()->name }}!</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('trips.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Add Trip</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="stats-number">{{ $stats['total_trips'] }}</div>
                    <div class="stats-label">Total Trips</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="stats-number">{{ $stats['current_year_trips'] }}</div>
                    <div class="stats-label">This Year</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="stats-number">{{ number_format($stats['current_year_miles'], 1) }}</div>
                    <div class="stats-label">Miles This Year</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="stats-number">${{ number_format($stats['current_year_miles'] * $currentYearRate->rate_in_dollars, 2) }}</div>
                    <div class="stats-label">Year Deduction</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4 d-md-none">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <a href="{{ route('trips.create') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus-circle"></i><br>
                                <small>Add Trip</small>
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-file-earmark-text"></i><br>
                                <small>Reports</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('import.index') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-upload"></i><br>
                                <small>Import</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('mileage-rates.index') }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-gear"></i><br>
                                <small>Settings</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Trips -->
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Trips</h5>
                    <a href="{{ route('trips.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentTrips->count() > 0)
                        <div class="row">
                            @foreach($recentTrips as $trip)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card trip-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <small class="text-muted">{{ $trip->trip_date->format('M j, Y') }}</small>
                                                <small class="text-primary fw-bold">${{ number_format($trip->irs_rated_cost, 2) }}</small>
                                            </div>
                                            
                                            <h6 class="card-title">
                                                <i class="bi bi-geo-alt text-success"></i> {{ Str::limit($trip->start_location, 20) }}
                                            </h6>
                                            <p class="card-text">
                                                <i class="bi bi-geo-alt-fill text-danger"></i> {{ Str::limit($trip->end_location, 20) }}
                                            </p>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary">{{ $trip->mileage }} mi</span>
                                                <div>
                                                    @foreach($trip->labels as $label)
                                                        <span class="label-badge" style="background-color: {{ $label->color }}20; color: {{ $label->color }};">
                                                            {{ $label->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            
                                            @if($trip->purpose)
                                                <small class="text-muted d-block mt-2">{{ Str::limit($trip->purpose, 50) }}</small>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="{{ route('trips.show', $trip) }}" class="btn btn-sm btn-outline-primary w-100">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-map text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No trips yet</h5>
                            <p class="text-muted">Start tracking your business miles!</p>
                            <a href="{{ route('trips.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Your First Trip
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
