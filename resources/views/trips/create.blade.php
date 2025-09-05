@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            @isset($tripData)
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle"></i>
                    <strong>Creating Next Leg:</strong> Starting from the previous trip's destination with the same date and labels.
                </div>
            @endisset
            <h1 class="h3 mb-0">
                @isset($tripData) Continue Journey @else Add New Trip @endisset
            </h1>
            <p class="text-muted">
                @isset($tripData) 
                    Add the next leg of your multi-stop journey
                @else 
                    Track your business mileage
                @endisset
            </p>
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

                    <form method="POST" action="{{ route('trips.store') }}" id="trip-form">
                        @csrf
                        
                        <!-- Location Inputs -->
                        <div class="row mb-3">
                            <div class="col-12 mb-3">
                                <label for="start_location" class="form-label">Start Location</label>
                                <div class="location-input">
                                    <input type="text" 
                                           class="form-control @error('start_location') is-invalid @enderror" 
                                           id="start_location" 
                                           name="start_location" 
                                                                                  value="{{ old('start_location', $tripData['start_location'] ?? '') }}" 
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
                                           value="{{ old('end_location') }}" 
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
                                <i class="bi bi-map"></i> <span id="route-btn-text">Calculate Route & Mileage</span>
                            </button>
                            <small class="form-text text-muted text-center d-block mt-1">
                                <i class="bi bi-info-circle"></i> Route calculates automatically when both addresses are entered
                            </small>
                        </div>

                        <!-- Mileage -->
                        <div class="mb-3">
                            <label for="mileage" class="form-label">Mileage</label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('mileage') is-invalid @enderror" 
                                       id="mileage" 
                                       name="mileage" 
                                       value="{{ old('mileage') }}" 
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
                                       value="{{ old('trip_date', $tripData['trip_date'] ?? date('Y-m-d')) }}" 
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
                                       value="{{ old('trip_time', $tripData['trip_time'] ?? date('H:i')) }}" 
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
                                   value="{{ old('purpose') }}" 
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
                                      placeholder="Additional trip details...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hidden coordinate fields -->
                        <input type="hidden" name="start_latitude" id="start_latitude" value="{{ old('start_latitude', $tripData['start_latitude'] ?? '') }}">
                        <input type="hidden" name="start_longitude" id="start_longitude" value="{{ old('start_longitude', $tripData['start_longitude'] ?? '') }}">
                        <input type="hidden" name="end_latitude" id="end_latitude" value="{{ old('end_latitude', '') }}">
                        <input type="hidden" name="end_longitude" id="end_longitude" value="{{ old('end_longitude', '') }}">

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-6">
                                <a href="{{ route('trips.index') }}" class="btn btn-outline-secondary w-100">Cancel</a>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle"></i> Save Trip
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
            <div class="card mt-3" id="route-info" style="display: none;">
                <div class="card-body">
                    <h6>Route Information</h6>
                    <div class="row">
                        <div class="col-6">
                            <strong>Distance:</strong> <span id="route-distance">-</span>
                        </div>
                        <div class="col-6">
                            <strong>Duration:</strong> <span id="route-duration">-</span>
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
let startMarker, endMarker;

// Global variable to store user's location for autocomplete biasing
let userLocation = null;

// Initialize map and autocomplete
function initTripMapAndAutocomplete() {
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: { lat: 37.7749, lng: -122.4194 } // Default to San Francisco
    });

    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
        draggable: true,
        suppressMarkers: false
    });
    directionsRenderer.setMap(map);

    // Set up autocomplete (initial setup without location bias)
    setupAutocomplete();
    
    // Set up event listeners
    setupEventListeners();

    // Try to get user's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            
            // Store user location globally
            userLocation = pos;
            
            // Center map on user location
            map.setCenter(pos);
            
            // Update autocomplete with location bias
            updateAutocompleteLocationBias();
            
        }, function(error) {
            console.log('Geolocation error:', error.message);
            // Fallback: continue without location bias
        });
    }
}

function setupAutocomplete() {
    // Use the current Places API (not deprecated)
    const autocompleteOptions = {
        types: [],
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
        console.log('Start place selected:', place);
        
        if (place.geometry && place.geometry.location) {
            document.getElementById('start_latitude').value = place.geometry.location.lat();
            document.getElementById('start_longitude').value = place.geometry.location.lng();
            console.log('Start coordinates saved:', place.geometry.location.lat(), place.geometry.location.lng());
            
            // Auto-calculate route if both locations are present
            checkAndAutoCalculateRoute();
        } else {
            console.warn('No geometry data for start location');
        }
    });

    endAutocomplete.addListener('place_changed', function() {
        const place = endAutocomplete.getPlace();
        console.log('End place selected:', place);
        
        if (place.geometry && place.geometry.location) {
            document.getElementById('end_latitude').value = place.geometry.location.lat();
            document.getElementById('end_longitude').value = place.geometry.location.lng();
            console.log('End coordinates saved:', place.geometry.location.lat(), place.geometry.location.lng());
            
            // Auto-calculate route if both locations are present
            checkAndAutoCalculateRoute();
        } else {
            console.warn('No geometry data for end location');
        }
    });
}

// Function to update autocomplete with location bias when user location is available
function updateAutocompleteLocationBias() {
    if (userLocation && startAutocomplete && endAutocomplete) {
        // Create a circular bias around user's location (approximately 50km radius)
        const circle = new google.maps.Circle({
            center: userLocation,
            radius: 50000 // 50km in meters
        });
        
        // Set bounds for location bias
        const bounds = circle.getBounds();
        
        // Update both autocomplete instances with location bias
        startAutocomplete.setBounds(bounds);
        endAutocomplete.setBounds(bounds);
        
        console.log('Autocomplete location bias enabled for:', userLocation);
    }
}

function setupEventListeners() {
    document.getElementById('calculate-route').addEventListener('click', calculateRoute);
    
    // Add listeners for manual address entry (not just autocomplete)
    document.getElementById('start_location').addEventListener('blur', function() {
        setTimeout(checkAndAutoCalculateRoute, 500); // Small delay to allow autocomplete
    });
    
    document.getElementById('end_location').addEventListener('blur', function() {
        setTimeout(checkAndAutoCalculateRoute, 500); // Small delay to allow autocomplete
    });
    
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

    const request = {
        origin: start,
        destination: end,
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.IMPERIAL
    };

    directionsService.route(request, function(result, status) {
        if (status === 'OK') {
            directionsRenderer.setDirections(result);
            document.getElementById('route-info').style.display = 'block';
        } else {
            console.error('Could not calculate route: ' + status);
            updateRouteButtonState('error', 'Route calculation failed');
            
            // Only show alert for manual clicks, not auto-calculations
            if (event && event.target && event.target.id === 'calculate-route') {
                alert('Could not calculate route: ' + status);
            }
        }
    });
}

function updateRouteInfo(route) {
    const leg = route.legs[0];
    const distance = leg.distance.text;
    const duration = leg.duration.text;
    
    document.getElementById('route-distance').textContent = distance;
    document.getElementById('route-duration').textContent = duration;
    
    // Extract mileage and update form
    const mileage = parseFloat(distance.replace(/[^\d.]/g, ''));
    if (mileage) {
        document.getElementById('mileage').value = mileage.toFixed(1);
        
        // Show success feedback
        updateRouteButtonState('success', 'Route Calculated');
        
        // Flash the mileage field to draw attention
        const mileageField = document.getElementById('mileage');
        mileageField.classList.add('bg-success', 'bg-opacity-25');
        setTimeout(() => {
            mileageField.classList.remove('bg-success', 'bg-opacity-25');
        }, 2000);
    }
}

// Auto-calculation functions
function checkAndAutoCalculateRoute() {
    const start = document.getElementById('start_location').value.trim();
    const end = document.getElementById('end_location').value.trim();
    
    if (start && end && start.length > 5 && end.length > 5) {
        console.log('Both addresses present, auto-calculating route...');
        updateRouteButtonState('calculating', 'Calculating Route...');
        calculateRoute();
    }
}

function updateRouteButtonState(state, text) {
    const btn = document.getElementById('calculate-route');
    const btnText = document.getElementById('route-btn-text');
    
    // Reset classes
    btn.className = 'btn w-100';
    
    switch (state) {
        case 'calculating':
            btn.classList.add('btn-warning');
            btn.disabled = true;
            btnText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + text;
            break;
        case 'success':
            btn.classList.add('btn-success');
            btn.disabled = false;
            btnText.innerHTML = '<i class="bi bi-check-circle me-2"></i>' + text;
            // Reset to normal after 3 seconds
            setTimeout(() => {
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-primary');
                btnText.innerHTML = '<i class="bi bi-map"></i> Recalculate Route';
            }, 3000);
            break;
        case 'error':
            btn.classList.add('btn-outline-danger');
            btn.disabled = false;
            btnText.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>' + text;
            break;
        default:
            btn.classList.add('btn-outline-primary');
            btn.disabled = false;
            btnText.innerHTML = '<i class="bi bi-map"></i> Calculate Route & Mileage';
    }
}

// Labels management with autocomplete
let selectedLabels = [];
let availableLabels = @json($labels ?? []);
let currentSuggestions = [];
let selectedSuggestionIndex = -1;

// Enhanced labels input with autocomplete
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
    // Delay to allow clicking on suggestions
    setTimeout(() => {
        if (!suggestionsDropdown.matches(':hover')) {
            hideSuggestions();
            addLabel();
        }
    }, 200);
});

// Label autocomplete functions
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
    
    // Update visual selection
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
    // Pre-populate labels if this is a "create from existing trip"
    @isset($tripData['selected_labels'])
        selectedLabels = @json($tripData['selected_labels']);
        renderLabels();
    @endisset
    
    // Initialize Google Maps if available
    if (typeof google !== 'undefined' && google.maps) {
        initTripMap();
    } else {
        console.log('Google Maps API not loaded yet, waiting for callback...');
    }
});

// Override the global initMap function for this page
window.initMap = function() {
    try {
        console.log('Google Maps API loaded, initializing map and autocomplete for trips...');
        initTripMapAndAutocomplete();
        console.log('Map and autocomplete initialization completed');
    } catch (error) {
        console.error('Error initializing Google Maps:', error);
        // Provide fallback message to user
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
            mapContainer.innerHTML = '<div class="text-center p-4"><i class="bi bi-exclamation-triangle text-warning"></i><br>Map temporarily unavailable</div>';
        }
    }
};
</script>
@endpush
