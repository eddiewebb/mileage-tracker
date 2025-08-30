<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Trip Reports</h1>
            <p class="text-muted">Generate detailed mileage reports for tax purposes</p>
        </div>
    </div>

    <!-- Report Generation Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Generate Report</h5>
                </div>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('reports.generate')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Date Range -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" 
                                       class="form-control <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="start_date" 
                                       name="start_date" 
                                       value="<?php echo e(old('start_date', date('Y-01-01'))); ?>" 
                                       required>
                                <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" 
                                       class="form-control <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                       id="end_date" 
                                       name="end_date" 
                                       value="<?php echo e(old('end_date', date('Y-m-d'))); ?>" 
                                       required>
                                <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <!-- Quick Date Range Buttons -->
                        <div class="mb-4">
                            <label class="form-label">Quick Date Ranges</label>
                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-outline-secondary quick-date" data-range="this-month">This Month</button>
                                <button type="button" class="btn btn-outline-secondary quick-date" data-range="last-month">Last Month</button>
                                <button type="button" class="btn btn-outline-secondary quick-date" data-range="this-year">This Year</button>
                                <button type="button" class="btn btn-outline-secondary quick-date" data-range="last-year">Last Year</button>
                            </div>
                        </div>

                        <!-- Report Format -->
                        <div class="mb-4">
                            <label class="form-label">Report Format</label>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="format" 
                                               id="format_pdf" 
                                               value="pdf" 
                                               <?php echo e(old('format', 'pdf') === 'pdf' ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="format_pdf">
                                            <i class="bi bi-file-earmark-pdf text-danger"></i> PDF Report
                                            <small class="d-block text-muted">Formatted for printing and filing</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="radio" 
                                               name="format" 
                                               id="format_csv" 
                                               value="csv" 
                                               <?php echo e(old('format') === 'csv' ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="format_csv">
                                            <i class="bi bi-file-earmark-spreadsheet text-success"></i> CSV Export
                                            <small class="d-block text-muted">Spreadsheet compatible format</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Generate Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-download"></i> Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Preview/Summary -->
    <div class="row mt-4">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Report Preview</h5>
                </div>
                <div class="card-body">
                    <div id="report-preview">
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-file-earmark-text" style="font-size: 3rem;"></i>
                            <p class="mt-2">Select date range and format to preview report contents</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
            
            // Update preview
            updateReportPreview();
        });
    });
    
    // Update preview when dates change
    document.getElementById('start_date').addEventListener('change', updateReportPreview);
    document.getElementById('end_date').addEventListener('change', updateReportPreview);
    
    // Initial preview update
    updateReportPreview();
});

function getDateRange(range) {
    const now = new Date();
    let start, end;
    
    switch (range) {
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
        case 'last-year':
            start = new Date(now.getFullYear() - 1, 0, 1);
            end = new Date(now.getFullYear() - 1, 11, 31);
            break;
        default:
            return { start: '', end: '' };
    }
    
    return {
        start: start.toISOString().split('T')[0],
        end: end.toISOString().split('T')[0]
    };
}

function updateReportPreview() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (!startDate || !endDate) {
        return;
    }
    
    // Simple preview - in a real app, you might fetch actual data
    const preview = document.getElementById('report-preview');
    preview.innerHTML = `
        <div class="row">
            <div class="col-md-4 text-center">
                <h6 class="text-muted">Date Range</h6>
                <p>${formatDate(startDate)} to ${formatDate(endDate)}</p>
            </div>
            <div class="col-md-4 text-center">
                <h6 class="text-muted">Report Format</h6>
                <p>${document.querySelector('input[name="format"]:checked').value.toUpperCase()}</p>
            </div>
            <div class="col-md-4 text-center">
                <h6 class="text-muted">Status</h6>
                <p class="text-success">Ready to Generate</p>
            </div>
        </div>
    `;
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/eddie/Development/mileage/resources/views/reports/index.blade.php ENDPATH**/ ?>