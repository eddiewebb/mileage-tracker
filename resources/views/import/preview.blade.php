@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('import.index') }}">Import</a></li>
                    <li class="breadcrumb-item active">Preview</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Import Preview</h1>
            <p class="text-muted">Review your data before importing</p>
        </div>
    </div>

    <!-- Import Summary -->
    <div class="row mb-4">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="stats-number">{{ $preview['total_records'] }}</div>
                            <div class="stats-label">Total Records</div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-number text-success">{{ $preview['valid_count'] }}</div>
                            <div class="stats-label">Valid Trips</div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-number text-danger">{{ $preview['error_count'] }}</div>
                            <div class="stats-label">Errors</div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-number text-primary">{{ array_sum(array_column($preview['valid'], 'mileage')) }}</div>
                            <div class="stats-label">Total Miles</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Errors (if any) -->
    @if($preview['error_count'] > 0)
        <div class="row mb-4">
            <div class="col">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-exclamation-triangle"></i> Import Errors
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">The following records have errors and will not be imported:</p>
                        
                        @foreach($preview['errors'] as $error)
                            <div class="alert alert-warning">
                                <strong>Row {{ $error['row'] }}:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($error['errors'] as $errorMsg)
                                        <li>{{ $errorMsg }}</li>
                                    @endforeach
                                </ul>
                                <small class="text-muted d-block mt-2">
                                    Data: {{ json_encode($error['record']) }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Valid Trips Preview -->
    @if($preview['valid_count'] > 0)
        <div class="row mb-4">
            <div class="col">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-check-circle"></i> Valid Trips to Import
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th class="text-end">Miles</th>
                                        <th>Purpose</th>
                                        <th>Tags</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($preview['valid'] as $trip)
                                        <tr>
                                            <td>{{ $trip['date'] }}</td>
                                            <td>{{ Str::limit($trip['start'], 30) }}</td>
                                            <td>{{ Str::limit($trip['end'], 30) }}</td>
                                            <td class="text-end">{{ $trip['mileage'] }}</td>
                                            <td>{{ Str::limit($trip['purpose'], 20) }}</td>
                                            <td>
                                                @if(!empty($trip['tags']))
                                                    @foreach($trip['tags'] as $tag)
                                                        <span class="badge bg-secondary me-1">{{ $tag }}</span>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($trip['notes'], 30) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirm Import -->
        <div class="row">
            <div class="col text-center">
                <form method="POST" action="{{ route('import.confirm') }}">
                    @csrf
                    
                    <!-- Hidden fields with valid trip data -->
                    @foreach($preview['valid'] as $index => $trip)
                        <input type="hidden" name="trips[{{ $index }}][date]" value="{{ $trip['date'] }}">
                        <input type="hidden" name="trips[{{ $index }}][start]" value="{{ $trip['start'] }}">
                        <input type="hidden" name="trips[{{ $index }}][end]" value="{{ $trip['end'] }}">
                        <input type="hidden" name="trips[{{ $index }}][mileage]" value="{{ $trip['mileage'] }}">
                        <input type="hidden" name="trips[{{ $index }}][purpose]" value="{{ $trip['purpose'] }}">
                        <input type="hidden" name="trips[{{ $index }}][notes]" value="{{ $trip['notes'] }}">
                        @if(!empty($trip['tags']))
                            @foreach($trip['tags'] as $tag)
                                <input type="hidden" name="trips[{{ $index }}][tags][]" value="{{ $tag }}">
                            @endforeach
                        @endif
                    @endforeach
                    
                    <div class="btn-group" role="group">
                        <a href="{{ route('import.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-left"></i> Back to Upload
                        </a>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle"></i> Confirm Import ({{ $preview['valid_count'] }} trips)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <!-- No Valid Records -->
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                        <h4 class="text-warning mt-3">No Valid Records Found</h4>
                        <p class="text-muted">
                            All records in your CSV file contain errors. 
                            Please fix the issues and try uploading again.
                        </p>
                        <a href="{{ route('import.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Back to Upload
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
