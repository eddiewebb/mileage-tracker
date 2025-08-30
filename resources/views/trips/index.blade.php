@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Your Trips</h1>
            <p class="text-muted">Manage your business mileage records</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('trips.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Add Trip</span>
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('trips.index') }}" class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Search trips by location, purpose, or notes...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-search"></i> <span class="d-none d-sm-inline">Search</span>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('trips.index') }}" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-x-circle"></i> <span class="d-none d-sm-inline">Clear</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Trips List -->
    @if($trips->count() > 0)
        <div class="row">
            @foreach($trips as $trip)
                <div class="col-lg-6 col-xl-4 mb-3">
                    <div class="card trip-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> {{ $trip->trip_date->format('M j, Y') }}
                                    <i class="bi bi-clock ms-2"></i> {{ $trip->trip_time->format('g:i A') }}
                                </small>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('trips.show', $trip) }}">
                                            <i class="bi bi-eye"></i> View
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('trips.edit', $trip) }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="{{ route('trips.destroy', $trip) }}" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <h6 class="card-title">
                                <i class="bi bi-geo-alt text-success"></i> 
                                {{ Str::limit($trip->start_location, 25) }}
                            </h6>
                            
                            <p class="card-text">
                                <i class="bi bi-geo-alt-fill text-danger"></i> 
                                {{ Str::limit($trip->end_location, 25) }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary fs-6">{{ $trip->mileage }} miles</span>
                                <span class="text-success fw-bold">${{ number_format($trip->irs_rated_cost, 2) }}</span>
                            </div>
                            
                            <!-- Labels -->
                            @if($trip->labels->count() > 0)
                                <div class="mb-2">
                                    @foreach($trip->labels as $label)
                                        <span class="label-badge" style="background-color: {{ $label->color }}20; color: {{ $label->color }};">
                                            {{ $label->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            
                            <!-- Purpose -->
                            @if($trip->purpose)
                                <p class="card-text mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-briefcase"></i> {{ Str::limit($trip->purpose, 40) }}
                                    </small>
                                </p>
                            @endif
                            
                            <!-- Notes -->
                            @if($trip->notes)
                                <p class="card-text">
                                    <small class="text-muted">{{ Str::limit($trip->notes, 60) }}</small>
                                </p>
                            @endif
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <a href="{{ route('trips.show', $trip) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col">
                {{ $trips->links() }}
            </div>
        </div>
    @else
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-map text-muted" style="font-size: 4rem;"></i>
                        <h4 class="text-muted mt-3">
                            @if(request('search'))
                                No trips found matching "{{ request('search') }}"
                            @else
                                No trips recorded yet
                            @endif
                        </h4>
                        <p class="text-muted">
                            @if(request('search'))
                                Try a different search term or clear the filter.
                            @else
                                Start tracking your business mileage today!
                            @endif
                        </p>
                        <a href="{{ route('trips.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add Your First Trip
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
