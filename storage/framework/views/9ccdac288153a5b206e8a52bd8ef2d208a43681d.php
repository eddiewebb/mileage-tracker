<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Dashboard</h1>
            <p class="text-muted">Welcome back, <?php echo e(Auth::user()->name); ?>!</p>
        </div>
        <div class="col-auto">
            <a href="<?php echo e(route('trips.create')); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Add Trip</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="stats-number"><?php echo e($stats['total_trips']); ?></div>
                    <div class="stats-label">Total Trips</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="stats-number"><?php echo e($stats['current_year_trips']); ?></div>
                    <div class="stats-label">This Year</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="stats-number"><?php echo e(number_format($stats['current_year_miles'], 1)); ?></div>
                    <div class="stats-label">Miles This Year</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="stats-number">$<?php echo e(number_format($stats['current_year_miles'] * $currentYearRate->rate_in_dollars, 2)); ?></div>
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
                            <a href="<?php echo e(route('trips.create')); ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus-circle"></i><br>
                                <small>Add Trip</small>
                            </a>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-success w-100">
                                <i class="bi bi-file-earmark-text"></i><br>
                                <small>Reports</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?php echo e(route('import.index')); ?>" class="btn btn-outline-info w-100">
                                <i class="bi bi-upload"></i><br>
                                <small>Import</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?php echo e(route('mileage-rates.index')); ?>" class="btn btn-outline-warning w-100">
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
                    <a href="<?php echo e(route('trips.index')); ?>" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if($recentTrips->count() > 0): ?>
                        <div class="row">
                            <?php $__currentLoopData = $recentTrips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card trip-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <small class="text-muted"><?php echo e($trip->trip_date->format('M j, Y')); ?></small>
                                                <small class="text-primary fw-bold">$<?php echo e(number_format($trip->irs_rated_cost, 2)); ?></small>
                                            </div>
                                            
                                            <h6 class="card-title">
                                                <i class="bi bi-geo-alt text-success"></i> <?php echo e(Str::limit($trip->start_location, 20)); ?>

                                            </h6>
                                            <p class="card-text">
                                                <i class="bi bi-geo-alt-fill text-danger"></i> <?php echo e(Str::limit($trip->end_location, 20)); ?>

                                            </p>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-primary"><?php echo e($trip->mileage); ?> mi</span>
                                                <div>
                                                    <?php $__currentLoopData = $trip->labels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span class="label-badge" style="background-color: <?php echo e($label->color); ?>20; color: <?php echo e($label->color); ?>;">
                                                            <?php echo e($label->name); ?>

                                                        </span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                            
                                            <?php if($trip->purpose): ?>
                                                <small class="text-muted d-block mt-2"><?php echo e(Str::limit($trip->purpose, 50)); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="<?php echo e(route('trips.show', $trip)); ?>" class="btn btn-sm btn-outline-primary w-100">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-map text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No trips yet</h5>
                            <p class="text-muted">Start tracking your business miles!</p>
                            <a href="<?php echo e(route('trips.create')); ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Your First Trip
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/eddie/Development/mileage/resources/views/dashboard.blade.php ENDPATH**/ ?>