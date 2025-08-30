<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('import.index')); ?>">Import</a></li>
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
                            <div class="stats-number"><?php echo e($preview['total_records']); ?></div>
                            <div class="stats-label">Total Records</div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-number text-success"><?php echo e($preview['valid_count']); ?></div>
                            <div class="stats-label">Valid Trips</div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-number text-danger"><?php echo e($preview['error_count']); ?></div>
                            <div class="stats-label">Errors</div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-number text-primary"><?php echo e(array_sum(array_column($preview['valid'], 'mileage'))); ?></div>
                            <div class="stats-label">Total Miles</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Errors (if any) -->
    <?php if($preview['error_count'] > 0): ?>
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
                        
                        <?php $__currentLoopData = $preview['errors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="alert alert-warning">
                                <strong>Row <?php echo e($error['row']); ?>:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php $__currentLoopData = $error['errors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $errorMsg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($errorMsg); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                                <small class="text-muted d-block mt-2">
                                    Data: <?php echo e(json_encode($error['record'])); ?>

                                </small>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Valid Trips Preview -->
    <?php if($preview['valid_count'] > 0): ?>
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
                                    <?php $__currentLoopData = $preview['valid']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($trip['date']); ?></td>
                                            <td><?php echo e(Str::limit($trip['start'], 30)); ?></td>
                                            <td><?php echo e(Str::limit($trip['end'], 30)); ?></td>
                                            <td class="text-end"><?php echo e($trip['mileage']); ?></td>
                                            <td><?php echo e(Str::limit($trip['purpose'], 20)); ?></td>
                                            <td>
                                                <?php if(!empty($trip['tags'])): ?>
                                                    <?php $__currentLoopData = $trip['tags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span class="badge bg-secondary me-1"><?php echo e($tag); ?></span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e(Str::limit($trip['notes'], 30)); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <form method="POST" action="<?php echo e(route('import.confirm')); ?>">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Hidden fields with valid trip data -->
                    <?php $__currentLoopData = $preview['valid']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $trip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <input type="hidden" name="trips[<?php echo e($index); ?>][date]" value="<?php echo e($trip['date']); ?>">
                        <input type="hidden" name="trips[<?php echo e($index); ?>][start]" value="<?php echo e($trip['start']); ?>">
                        <input type="hidden" name="trips[<?php echo e($index); ?>][end]" value="<?php echo e($trip['end']); ?>">
                        <input type="hidden" name="trips[<?php echo e($index); ?>][mileage]" value="<?php echo e($trip['mileage']); ?>">
                        <input type="hidden" name="trips[<?php echo e($index); ?>][purpose]" value="<?php echo e($trip['purpose']); ?>">
                        <input type="hidden" name="trips[<?php echo e($index); ?>][notes]" value="<?php echo e($trip['notes']); ?>">
                        <?php if(!empty($trip['tags'])): ?>
                            <?php $__currentLoopData = $trip['tags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <input type="hidden" name="trips[<?php echo e($index); ?>][tags][]" value="<?php echo e($tag); ?>">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <div class="btn-group" role="group">
                        <a href="<?php echo e(route('import.index')); ?>" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-arrow-left"></i> Back to Upload
                        </a>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle"></i> Confirm Import (<?php echo e($preview['valid_count']); ?> trips)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
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
                        <a href="<?php echo e(route('import.index')); ?>" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Back to Upload
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/eddie/Development/mileage/resources/views/import/preview.blade.php ENDPATH**/ ?>