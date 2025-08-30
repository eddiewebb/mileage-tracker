@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('trips.index') }}">Trips</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('trips.show', $trip) }}">Trip Details</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Edit Trip</h1>
            <p class="text-muted">Update trip details</p>
        </div>
    </div>

    <div class="row">
        <!-- Trip Form -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Trip Details</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('trips.update', $trip) }}" id="trip-form">
                        @csrf
                        @method('PUT')
                        
                        <!-- Location Inputs -->
                        <div class="row mb-3">
                            <div class="col-12 mb-3">
                                <label for="start_location" class="form-label">Start Location</label>
                                <div class="location-input">
                                    <input type="text" 
                                           class="form-control @error('start_location') is-invalid @enderror" 
                                           id="start_location" 
                                           name="start_location" 
                                           value="{{ old('start_location', $trip->start_location) }}" 
                                           placeholder="Enter starting address"
                                           required>
                                    <div class="suggestions-dropdown" id="start-suggestions"></div>
                                </div>
                                @error('start_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label for="end_location" class="form-label">End Location</label>
                                <div class="location-input">
                                    <input type="text" 
                                           class="form-control @error('end_location') is-invalid @enderror" 
                                           id="end_location" 
                                           name="end_location" 
                                           value="{{ old('end_location', $trip->end_location) }}" 
                                           placeholder="Enter destination address"
                                           required>
                                    <div class="suggestions-dropdown" id="end-suggestions"></div>
                                </div>
                                @error('end_location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Route Actions -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary w-100" id="calculate-route">
                                <i class="bi bi-map"></i> Recalculate Route & Mileage
                            </button>
                        </div>

                        <!-- Mileage -->
                        <div class="mb-3">
                            <label for="mileage" class="form-label">Mileage</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('mileage') is-invalid @enderror" 
                                       id="mileage" 
                                       name="mileage" 
                                       value="{{ old('mileage', $trip->mileage) }}" 
                                       step="0.1" 
                                       min="0.1"
                                       placeholder="0.0"
                                       required>
                                <span class="input-group-text">miles</span>
                            </div>
                            @error('mileage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date and Time -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <label for="trip_date" class="form-label">Date</label>
                                <input type="date" 
                                       class="form-control @error('trip_date') is-invalid @enderror" 
                                       id="trip_date" 
                                       name="trip_date" 
                                       value="{{ old('trip_date', $trip->trip_date->format('Y-m-d')) }}" 
                                       required>
                                @error('trip_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="trip_time" class="form-label">Time</label>
                                <input type="time" 
                                       class="form-control @error('trip_time') is-invalid @enderror" 
                                       id="trip_time" 
                                       name="trip_time" 
                                       value="{{ old('trip_time', $trip->trip_time->format('H:i')) }}" 
                                       required>
                                @error('trip_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Purpose -->
                        <div class="mb-3">
                            <label for="purpose" class="form-label">Trip Purpose</label>
                            <input type="text" 
                                   class="form-control @error('purpose') is-invalid @enderror" 
                                   id="purpose" 
                                   name="purpose" 
                                   value="{{ old('purpose', $trip->purpose) }}" 
                                   placeholder="e.g., Client meeting, Sales call">
                            @error('purpose')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Labels -->
                        <div class="mb-3">
                            <label for="labels" class="form-label">Labels/Tags</label>
                            <div class="position-relative">
                                <input type="text" 
                                       class="form-control" 
                                       id="labels-input" 
                                       placeholder="Type to search existing tags or create new ones"
                                       autocomplete="off">
                                <div id="labels-suggestions" class="labels-autocomplete-dropdown"></div>
                            </div>
                            <small class="form-text text-muted">
                                Start typing to see existing labels or press Enter to create new ones.
                            </small>
                            <div id="labels-container" class="mt-2"></div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="Additional trip details...">{{ old('notes', $trip->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hidden coordinate fields -->
                        <input type="hidden" name="start_latitude" id="start_latitude" value="{{ $trip->start_latitude }}">
                        <input type="hidden" name="start_longitude" id="start_longitude" value="{{ $trip->start_longitude }}">
                        <input type="hidden" name="end_latitude" id="end_latitude" value="{{ $trip->end_latitude }}">
                        <input type="hidden" name="end_longitude" id="end_longitude" value="{{ $trip->end_longitude }}">

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-4">
                                <a href="{{ route('trips.show', $trip) }}" class="btn btn-outline-secondary w-100">Cancel</a>
                            </div>
                            <div class="col-8">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle"></i> Update Trip
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Route Preview</h5>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 400px; border-radius: 0 0 12px 12px;"></div>
                </div>
            </div>
            
            <!-- Route Info -->
            <div class="card mt-3" id="route-info">
                <div class="card-body">
                    <h6>Current Route Information</h6>
                    <div class="row">
                        <div class="col-6">
                            <strong>Distance:</strong> <span id="route-distance">{{ $trip->mileage }} miles</span>
                        </div>
                        <div class="col-6">
                            <strong>IRS Cost:</strong> <span id="route-cost">${{ number_format($trip->irs_rated_cost, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let map, directionsService, directionsRenderer;
let startAutocomplete, endAutocomplete;

// Initialize map with existing route
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: { lat: 37.7749, lng: -122.4194 }
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        draggable: true,
        suppressMarkers: false
    });
    directionsRenderer.setMap(map);

    // Set up autocomplete
    setupAutocomplete();
    
    // Set up event listeners
    setupEventListeners();
    
    // Load existing route
    loadExistingRoute();
    
    // Initialize labels with existing data
    initializeExistingLabels();
}

function loadExistingRoute() {
    const startLocation = @json($trip->start_location);
    const endLocation = @json($trip->end_location);

    if (startLocation && endLocation) {
        calculateRouteFromLocations(startLocation, endLocation);
    }
}

function initializeExistingLabels() {
    @foreach($trip->labels as $label)
        selectedLabels.push(@json($label->name));
    @endforeach
    renderLabels();
}

// ... (rest of the JavaScript from create.blade.php with modifications for edit)
let selectedLabels = [];

// Copy all the JavaScript functions from create.blade.php here
function setupAutocomplete() {
    // Use the current Places API (not deprecated)
    const autocompleteOptions = {
        types: ['address'],
        componentRestrictions: { country: 'us' },
        fields: ['place_id', 'formatted_address', 'geometry.location', 'name']
    };
    
    startAutocomplete = new google.maps.places.Autocomplete(
        document.getElementById('start_location'),
        autocompleteOptions
    );
    
    endAutocomplete = new google.maps.places.Autocomplete(
        document.getElementById('end_location'),
        autocompleteOptions
    );

    startAutocomplete.addListener('place_changed', function() {
        const place = startAutocomplete.getPlace();
        if (place.geometry) {
            document.getElementById('start_latitude').value = place.geometry.location.lat();
            document.getElementById('start_longitude').value = place.geometry.location.lng();
        }
    });

    endAutocomplete.addListener('place_changed', function() {
        const place = endAutocomplete.getPlace();
        if (place.geometry) {
            document.getElementById('end_latitude').value = place.geometry.location.lat();
            document.getElementById('end_longitude').value = place.geometry.location.lng();
        }
    });
}

function setupEventListeners() {
    document.getElementById('calculate-route').addEventListener('click', calculateRoute);
    
    directionsRenderer.addListener('directions_changed', function() {
        const directions = directionsRenderer.getDirections();
        const route = directions.routes[0];
        if (route) {
            updateRouteInfo(route);
        }
    });
}

function calculateRoute() {
    const start = document.getElementById('start_location').value;
    const end = document.getElementById('end_location').value;

    if (!start || !end) {
        alert('Please enter both start and end locations');
        return;
    }

    calculateRouteFromLocations(start, end);
}

function calculateRouteFromLocations(start, end) {
    const request = {
        origin: start,
        destination: end,
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.IMPERIAL
    };

    directionsService.route(request, function(result, status) {
        if (status === 'OK') {
            directionsRenderer.setDirections(result);
        } else {
            console.error('Could not calculate route: ' + status);
        }
    });
}

function updateRouteInfo(route) {
    const leg = route.legs[0];
    const distance = leg.distance.text;
    const duration = leg.duration.text;
    
    document.getElementById('route-distance').textContent = distance;
    
    // Extract mileage and update form
    const mileage = parseFloat(distance.replace(/[^\d.]/g, ''));
    if (mileage) {
        document.getElementById('mileage').value = mileage.toFixed(1);
    }
}

// Labels management with autocomplete (same as create form)
let availableLabels = @json($labels ?? []);
let currentSuggestions = [];
let selectedSuggestionIndex = -1;

const labelsInput = document.getElementById('labels-input');
const suggestionsDropdown = document.getElementById('labels-suggestions');

labelsInput.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        navigateSuggestions(1);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        navigateSuggestions(-1);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (selectedSuggestionIndex >= 0) {
            selectSuggestion(currentSuggestions[selectedSuggestionIndex]);
        } else {
            addLabel();
        }
    } else if (e.key === ',') {
        e.preventDefault();
        addLabel();
    } else if (e.key === 'Escape') {
        hideSuggestions();
    }
});

labelsInput.addEventListener('input', function() {
    const value = this.value.trim();
    if (value.length >= 1) {
        showLabelSuggestions(value);
    } else {
        hideSuggestions();
    }
});

labelsInput.addEventListener('blur', function() {
    setTimeout(() => {
        if (!suggestionsDropdown.matches(':hover')) {
            hideSuggestions();
            addLabel();
        }
    }, 200);
});

// Copy all autocomplete functions from create form
function showLabelSuggestions(searchText) {
    const filtered = availableLabels.filter(label => 
        label.name.toLowerCase().includes(searchText.toLowerCase()) &&
        !selectedLabels.includes(label.name)
    );
    
    currentSuggestions = filtered;
    selectedSuggestionIndex = -1;
    
    if (filtered.length > 0) {
        renderSuggestions(filtered);
        suggestionsDropdown.classList.add('show');
    } else {
        hideSuggestions();
    }
}

function renderSuggestions(suggestions) {
    suggestionsDropdown.innerHTML = '';
    
    suggestions.forEach((label, index) => {
        const div = document.createElement('div');
        div.className = 'label-suggestion';
        div.innerHTML = `
            <div class="label-color" style="background-color: ${label.color}"></div>
            <span>${label.name}</span>
        `;
        div.addEventListener('click', () => selectSuggestion(label));
        suggestionsDropdown.appendChild(div);
    });
}

function navigateSuggestions(direction) {
    if (currentSuggestions.length === 0) return;
    
    selectedSuggestionIndex += direction;
    
    if (selectedSuggestionIndex < 0) {
        selectedSuggestionIndex = currentSuggestions.length - 1;
    } else if (selectedSuggestionIndex >= currentSuggestions.length) {
        selectedSuggestionIndex = 0;
    }
    
    document.querySelectorAll('.label-suggestion').forEach((el, index) => {
        el.classList.toggle('selected', index === selectedSuggestionIndex);
    });
}

function selectSuggestion(label) {
    if (!selectedLabels.includes(label.name)) {
        selectedLabels.push(label.name);
        renderLabels();
    }
    
    labelsInput.value = '';
    hideSuggestions();
}

function hideSuggestions() {
    suggestionsDropdown.classList.remove('show');
    selectedSuggestionIndex = -1;
}

function addLabel() {
    const input = document.getElementById('labels-input');
    const labelText = input.value.trim().replace(/,$/, '');
    
    if (labelText && !selectedLabels.includes(labelText)) {
        selectedLabels.push(labelText);
        renderLabels();
        input.value = '';
        hideSuggestions();
    }
}

function removeLabel(labelText) {
    selectedLabels = selectedLabels.filter(label => label !== labelText);
    renderLabels();
}

function renderLabels() {
    const container = document.getElementById('labels-container');
    container.innerHTML = '';
    
    selectedLabels.forEach(labelName => {
        // Find the color for existing labels
        const existingLabel = availableLabels.find(l => l.name === labelName);
        const labelColor = existingLabel ? existingLabel.color : '#007bff';
        
        const badge = document.createElement('span');
        badge.className = 'badge me-1 mb-1';
        badge.style.backgroundColor = labelColor;
        badge.style.color = 'white';
        badge.innerHTML = `
            ${labelName}
            <button type="button" class="btn-close btn-close-white ms-1" style="font-size: 0.6rem;" onclick="removeLabel('${labelName}')"></button>
            <input type="hidden" name="labels[]" value="${labelName}">
        `;
        container.appendChild(badge);
    });
}

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (typeof google !== 'undefined') {
        initMap();
    }
});

window.initMap = initMap;
</script>
@endpush
