@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('trips.index') }}">Trips</a></li>
                    <li class="breadcrumb-item active">Trip Details</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Trip Details</h1>
            <p class="text-muted">{{ $trip->trip_date->format('F j, Y') }} at {{ $trip->trip_time->format('g:i A') }}</p>
        </div>
        <div class="col-auto">
            <div class="btn-group" role="group">
                <a href="{{ route('trips.edit', $trip) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Edit</span>
                </a>
                <form method="POST" action="{{ route('trips.destroy', $trip) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this trip?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Delete</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Trip Information -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Trip Information</h5>
                </div>
                <div class="card-body">
                    <!-- Route -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Route</h6>
                        <div class="mb-2">
                            <i class="bi bi-geo-alt text-success"></i>
                            <strong>From:</strong> {{ $trip->start_location }}
                        </div>
                        <div class="mb-2">
                            <i class="bi bi-geo-alt-fill text-danger"></i>
                            <strong>To:</strong> {{ $trip->end_location }}
                        </div>
                    </div>

                    <!-- Distance & Cost -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number text-primary">{{ $trip->mileage }}</div>
                                <div class="stats-label">Miles</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stats-card">
                                <div class="stats-number text-success">${{ number_format($trip->irs_rated_cost, 2) }}</div>
                                <div class="stats-label">IRS Deduction</div>
                            </div>
                        </div>
                    </div>

                    <!-- Date & Time -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Date & Time</h6>
                        <div class="mb-2">
                            <i class="bi bi-calendar"></i>
                            <strong>Date:</strong> {{ $trip->trip_date->format('l, F j, Y') }}
                        </div>
                        <div class="mb-2">
                            <i class="bi bi-clock"></i>
                            <strong>Time:</strong> {{ $trip->trip_time->format('g:i A') }}
                        </div>
                    </div>

                    <!-- Purpose -->
                    @if($trip->purpose)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Purpose</h6>
                            <p class="mb-0">
                                <i class="bi bi-briefcase"></i> {{ $trip->purpose }}
                            </p>
                        </div>
                    @endif

                    <!-- Labels -->
                    @if($trip->labels->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Labels</h6>
                            <div>
                                @foreach($trip->labels as $label)
                                    <span class="label-badge" style="background-color: {{ $label->color }}20; color: {{ $label->color }};">
                                        {{ $label->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Notes -->
                    @if($trip->notes)
                        <div class="mb-0">
                            <h6 class="text-muted mb-2">Notes</h6>
                            <p class="mb-0">{{ $trip->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Route Map</h5>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 400px; border-radius: 0 0 12px 12px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let map, directionsService, directionsRenderer;

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: { lat: 37.7749, lng: -122.4194 }
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        suppressMarkers: false
    });
    directionsRenderer.setMap(map);

    // Show the route for this trip
    showTripRoute();
}

function showTripRoute() {
    const startLocation = @json($trip->start_location);
    const endLocation = @json($trip->end_location);

    if (startLocation && endLocation) {
        const request = {
            origin: startLocation,
            destination: endLocation,
            travelMode: google.maps.TravelMode.DRIVING
        };

        directionsService.route(request, function(result, status) {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);
            } else {
                console.error('Could not display route: ' + status);
                
                // Fallback: show markers if route calculation fails
                showFallbackMarkers();
            }
        });
    } else {
        showFallbackMarkers();
    }
}

function showFallbackMarkers() {
    const startLat = @json($trip->start_latitude);
    const startLng = @json($trip->start_longitude);
    const endLat = @json($trip->end_latitude);
    const endLng = @json($trip->end_longitude);

    if (startLat && startLng) {
        new google.maps.Marker({
            position: { lat: parseFloat(startLat), lng: parseFloat(startLng) },
            map: map,
            title: 'Start: ' + @json($trip->start_location),
            icon: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png'
        });
    }

    if (endLat && endLng) {
        new google.maps.Marker({
            position: { lat: parseFloat(endLat), lng: parseFloat(endLng) },
            map: map,
            title: 'End: ' + @json($trip->end_location),
            icon: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
        });

        // Center map on end location
        map.setCenter({ lat: parseFloat(endLat), lng: parseFloat(endLng) });
    }
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (typeof google !== 'undefined') {
        initMap();
    }
});

window.initMap = initMap;
</script>
@endpush
