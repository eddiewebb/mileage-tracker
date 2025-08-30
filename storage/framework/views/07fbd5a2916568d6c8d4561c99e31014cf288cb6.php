<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Mileage Tracker')); ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }

        .btn {
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }

        .stats-card {
            text-align: center;
            padding: 1.5rem;
        }

        .stats-card .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .stats-card .stats-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
        }

        .trip-card {
            transition: transform 0.2s;
        }

        .trip-card:hover {
            transform: translateY(-2px);
        }

        .label-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            margin-right: 0.25rem;
            margin-bottom: 0.25rem;
            display: inline-block;
        }

        .mobile-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #dee2e6;
            padding: 0.5rem 0;
            z-index: 1000;
        }

        .mobile-nav .nav-link {
            text-align: center;
            padding: 0.5rem;
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 0.8rem;
        }

        .mobile-nav .nav-link.active {
            color: var(--primary-color);
        }

        .mobile-nav .nav-link i {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 0.25rem;
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding-bottom: 80px; /* Space for mobile nav */
            }
            
            .desktop-nav {
                display: none;
            }
        }

        @media (min-width: 769px) {
            .mobile-nav {
                display: none;
            }
        }

        .location-input {
            position: relative;
        }
        
        /* Labels Autocomplete Styling */
        .labels-autocomplete-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .labels-autocomplete-dropdown.show {
            display: block;
        }
        
        .label-suggestion {
            padding: 10px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f8f9fa;
            display: flex;
            align-items: center;
            transition: background-color 0.2s;
        }
        
        .label-suggestion:hover,
        .label-suggestion.selected {
            background-color: #f8f9fa;
        }
        
        .label-suggestion.selected {
            background-color: #007bff;
            color: white;
        }
        
        .label-suggestion .label-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .suggestions-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .suggestion-item {
            padding: 0.75rem;
            cursor: pointer;
            border-bottom: 1px solid #f8f9fa;
        }

        .suggestion-item:hover {
            background-color: #f8f9fa;
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <!-- Desktop Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary desktop-nav">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo e(route('dashboard')); ?>">
                <i class="bi bi-geo-alt"></i> Mileage Tracker
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('dashboard')); ?>">
                            <i class="bi bi-house"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('trips.*') ? 'active' : ''); ?>" href="<?php echo e(route('trips.index')); ?>">
                            <i class="bi bi-map"></i> Trips
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>" href="<?php echo e(route('reports.index')); ?>">
                            <i class="bi bi-file-earmark-text"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('mileage-rates.*') ? 'active' : ''); ?>" href="<?php echo e(route('mileage-rates.index')); ?>">
                            <i class="bi bi-currency-dollar"></i> Rates
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo e(request()->routeIs('import.*') ? 'active' : ''); ?>" href="<?php echo e(route('import.index')); ?>">
                            <i class="bi bi-upload"></i> Import
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i> <?php echo e(Auth::user()->name); ?>

                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <form method="POST" action="<?php echo e(route('logout')); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid py-4">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <!-- Mobile Navigation -->
    <nav class="mobile-nav d-md-none">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                        <i class="bi bi-house"></i>
                        <span>Home</span>
                    </a>
                </div>
                <div class="col">
                    <a href="<?php echo e(route('trips.create')); ?>" class="nav-link <?php echo e(request()->routeIs('trips.create') ? 'active' : ''); ?>">
                        <i class="bi bi-plus-circle"></i>
                        <span>Add Trip</span>
                    </a>
                </div>
                <div class="col">
                    <a href="<?php echo e(route('trips.index')); ?>" class="nav-link <?php echo e(request()->routeIs('trips.index') ? 'active' : ''); ?>">
                        <i class="bi bi-map"></i>
                        <span>Trips</span>
                    </a>
                </div>
                <div class="col">
                    <a href="<?php echo e(route('reports.index')); ?>" class="nav-link <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Reports</span>
                    </a>
                </div>
                <div class="col">
                    <a href="<?php echo e(route('mileage-rates.index')); ?>" class="nav-link <?php echo e(request()->routeIs('mileage-rates.*') ? 'active' : ''); ?>">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Google Maps API with Places Library for Address Autocomplete -->
    <?php if(config('services.google_maps.api_key')): ?>
        <script>
            window.googleMapsApiKey = '<?php echo e(config('services.google_maps.api_key')); ?>';
            // Fallback initMap for pages that don't define it
            window.initMap = window.initMap || function() {
                console.log('Google Maps API loaded (no specific map to initialize)');
            };
        </script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(config('services.google_maps.api_key')); ?>&libraries=places&callback=initMap&v=weekly"></script>
    <?php else: ?>
        <script>
            console.warn('Google Maps API key not configured. Add GOOGLE_MAPS_API_KEY to your .env file to enable location autocomplete.');
            // Provide fallback initMap function
            window.initMap = function() {
                console.log('Google Maps disabled - no API key');
            };
        </script>
    <?php endif; ?>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Users/eddie/Development/mileage/resources/views/layouts/app.blade.php ENDPATH**/ ?>