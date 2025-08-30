<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Your Trips</h1>
            <p class="text-muted">Manage your business mileage records</p>
        </div>
        <div class="col-auto">
            <a href="<?php echo e(route('trips.create')); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Add Trip</span>
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-funnel"></i> Filter Trips
                        <?php if(request()->hasAny(['search', 'start_date', 'end_date', 'tags'])): ?>
                            <span class="badge bg-primary ms-2"><?php echo e(collect([request('search'), request('start_date'), request('end_date'), request('tags')])->filter()->count()); ?> filters applied</span>
                        <?php endif; ?>
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?php echo e(route('trips.index')); ?>" id="filters-form">
                        <!-- Text Search -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="search" class="form-label">Search Text</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" 
                                           class="form-control" 
                                           id="search"
                                           name="search" 
                                           value="<?php echo e(request('search')); ?>" 
                                           placeholder="Search by location, purpose, or notes...">
                                </div>
                            </div>
                        </div>

                        <!-- Date Range -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="start_date"
                                       name="start_date" 
                                       value="<?php echo e(request('start_date')); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="end_date"
                                       name="end_date" 
                                       value="<?php echo e(request('end_date')); ?>">
                            </div>
                        </div>

                        <!-- Quick Date Range Buttons -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Quick Date Ranges</label>
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date" data-range="this-week">This Week</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date" data-range="this-month">This Month</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date" data-range="last-month">Last Month</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm quick-date" data-range="this-year">This Year</button>
                                </div>
                            </div>
                        </div>

                        <!-- Tag Filter -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="tags" class="form-label">Filter by Tags</label>
                                <select class="form-select" 
                                        id="tags" 
                                        name="tags[]" 
                                        multiple 
                                        data-placeholder="Select tags to filter by...">
                                    <?php $__currentLoopData = $userLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($label->name); ?>" 
                                                <?php echo e(in_array($label->name, request('tags', [])) ? 'selected' : ''); ?>

                                                data-color="<?php echo e($label->color); ?>">
                                            <?php echo e($label->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <small class="form-text text-muted">
                                    Hold Ctrl/Cmd to select multiple tags
                                </small>
                            </div>
                        </div>

                        <!-- Filter Actions -->
                        <div class="row">
                            <div class="col-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-funnel"></i> Apply Filters
                                </button>
                            </div>
                            <div class="col-6">
                                <a href="<?php echo e(route('trips.index')); ?>" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-x-circle"></i> Clear All
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Trips List -->
    <?php if($trips->count() > 0): ?>
        <div class="row">
            <?php $__currentLoopData = $trips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-lg-6 col-xl-4 mb-3">
                    <div class="card trip-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> <?php echo e($trip->trip_date->format('M j, Y')); ?>

                                    <i class="bi bi-clock ms-2"></i> <?php echo e($trip->trip_time->format('g:i A')); ?>

                                </small>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="<?php echo e(route('trips.show', $trip)); ?>">
                                            <i class="bi bi-eye"></i> View
                                        </a></li>
                                        <li><a class="dropdown-item text-success" href="<?php echo e(route('trips.create-from', $trip)); ?>">
                                            <i class="bi bi-arrow-right-circle"></i> Create Next Leg
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="<?php echo e(route('trips.edit', $trip)); ?>">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a></li>
                                        <li>
                                            <form method="POST" action="<?php echo e(route('trips.destroy', $trip)); ?>" onsubmit="return confirm('Are you sure?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
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
                                <?php echo e(Str::limit($trip->start_location, 25)); ?>

                            </h6>
                            
                            <p class="card-text">
                                <i class="bi bi-geo-alt-fill text-danger"></i> 
                                <?php echo e(Str::limit($trip->end_location, 25)); ?>

                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary fs-6"><?php echo e($trip->mileage); ?> miles</span>
                                <span class="text-success fw-bold">$<?php echo e(number_format($trip->irs_rated_cost, 2)); ?></span>
                            </div>
                            
                            <!-- Labels -->
                            <?php if($trip->labels->count() > 0): ?>
                                <div class="mb-2">
                                    <?php $__currentLoopData = $trip->labels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="label-badge" style="background-color: <?php echo e($label->color); ?>20; color: <?php echo e($label->color); ?>;">
                                            <?php echo e($label->name); ?>

                                        </span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Purpose -->
                            <?php if($trip->purpose): ?>
                                <p class="card-text mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-briefcase"></i> <?php echo e(Str::limit($trip->purpose, 40)); ?>

                                    </small>
                                </p>
                            <?php endif; ?>
                            
                            <!-- Notes -->
                            <?php if($trip->notes): ?>
                                <p class="card-text">
                                    <small class="text-muted"><?php echo e(Str::limit($trip->notes, 60)); ?></small>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-footer bg-transparent">
                            <a href="<?php echo e(route('trips.show', $trip)); ?>" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col">
                <?php echo e($trips->links()); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-map text-muted" style="font-size: 4rem;"></i>
                        <h4 class="text-muted mt-3">
                            <?php if(request('search')): ?>
                                No trips found matching "<?php echo e(request('search')); ?>"
                            <?php else: ?>
                                No trips recorded yet
                            <?php endif; ?>
                        </h4>
                        <p class="text-muted">
                            <?php if(request('search')): ?>
                                Try a different search term or clear the filter.
                            <?php else: ?>
                                Start tracking your business mileage today!
                            <?php endif; ?>
                        </p>
                        <a href="<?php echo e(route('trips.create')); ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add Your First Trip
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick date range handlers
    document.querySelectorAll('.quick-date').forEach(btn => {
        btn.addEventListener('click', function() {
            const range = this.dataset.range;
            const dates = getDateRange(range);
            
            document.getElementById('start_date').value = dates.start;
            document.getElementById('end_date').value = dates.end;
            
            // Highlight selected button
            document.querySelectorAll('.quick-date').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Auto-submit form when filters change (with debounce)
    let filterTimeout;
    const autoSubmitElements = ['start_date', 'end_date', 'tags'];
    
    autoSubmitElements.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            element.addEventListener('change', function() {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(() => {
                    document.getElementById('filters-form').submit();
                }, 300);
            });
        }
    });
    
    // Enhance multi-select tags dropdown
    const tagsSelect = document.getElementById('tags');
    if (tagsSelect) {
        // Add visual indicators for selected tags
        tagsSelect.addEventListener('change', function() {
            updateTagsDisplay();
        });
        
        // Initial display update
        updateTagsDisplay();
    }
});

function getDateRange(range) {
    const now = new Date();
    let start, end;
    
    switch (range) {
        case 'this-week':
            const startOfWeek = new Date(now);
            startOfWeek.setDate(now.getDate() - now.getDay());
            start = startOfWeek;
            end = new Date(startOfWeek);
            end.setDate(startOfWeek.getDate() + 6);
            break;
        case 'this-month':
            start = new Date(now.getFullYear(), now.getMonth(), 1);
            end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            break;
        case 'last-month':
            start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            end = new Date(now.getFullYear(), now.getMonth(), 0);
            break;
        case 'this-year':
            start = new Date(now.getFullYear(), 0, 1);
            end = new Date(now.getFullYear(), 11, 31);
            break;
        default:
            return { start: '', end: '' };
    }
    
    return {
        start: start.toISOString().split('T')[0],
        end: end.toISOString().split('T')[0]
    };
}

function updateTagsDisplay() {
    const tagsSelect = document.getElementById('tags');
    const selectedOptions = Array.from(tagsSelect.selectedOptions);
    
    // Update the label to show selected count
    const label = document.querySelector('label[for="tags"]');
    if (selectedOptions.length > 0) {
        label.innerHTML = `Filter by Tags <span class="badge bg-primary ms-1">${selectedOptions.length}</span>`;
    } else {
        label.textContent = 'Filter by Tags';
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/eddie/Development/mileage/resources/views/trips/index.blade.php ENDPATH**/ ?>