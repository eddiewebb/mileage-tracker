<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Import Trips</h1>
            <p class="text-muted">Import existing trip data from CSV files</p>
        </div>
    </div>

    <!-- Instructions -->
    <div class="row mb-4">
        <div class="col">
            <div class="alert alert-info">
                <h5><i class="bi bi-info-circle"></i> Import Instructions</h5>
                <p class="mb-2">Your CSV file should contain the following columns (headers can be case-insensitive):</p>
                <ul class="mb-3">
                    <li><strong>Date</strong> - Trip date (YYYY-MM-DD, MM/DD/YYYY, or similar formats)</li>
                    <li><strong>Start</strong> - Starting location address</li>
                    <li><strong>End</strong> - Destination address</li>
                    <li><strong>Mileage</strong> - Distance in miles (numeric)</li>
                    <li><strong>Notes</strong> - Trip notes (optional)</li>
                    <li><strong>Tags</strong> - Comma-separated labels (optional)</li>
                    <li><strong>Purpose</strong> - Trip purpose (optional)</li>
                </ul>
                <p class="mb-0">
                    <strong>Note:</strong> You'll be able to review all parsed data before confirming the import.
                </p>
            </div>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Upload CSV File</h5>
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

                    <form method="POST" action="<?php echo e(route('import.preview')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-4">
                            <label for="csv_file" class="form-label">Select CSV File</label>
                            <input type="file" 
                                   class="form-control <?php $__errorArgs = ['csv_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="csv_file" 
                                   name="csv_file" 
                                   accept=".csv,.txt"
                                   required>
                            <?php $__errorArgs = ['csv_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="form-text">
                                Maximum file size: 2MB. Supported formats: CSV, TXT
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-upload"></i> Upload & Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sample CSV Template -->
    <div class="row mt-4">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-download"></i> Sample CSV Template
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Download or copy this template to format your data correctly:</p>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Mileage</th>
                                    <th>Purpose</th>
                                    <th>Notes</th>
                                    <th>Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2024-01-15</td>
                                    <td>123 Main St, Anytown, CA</td>
                                    <td>456 Client Ave, Business City, CA</td>
                                    <td>25.3</td>
                                    <td>Client meeting</td>
                                    <td>Quarterly review meeting</td>
                                    <td>client, quarterly</td>
                                </tr>
                                <tr>
                                    <td>2024-01-16</td>
                                    <td>456 Client Ave, Business City, CA</td>
                                    <td>789 Office Blvd, Workplace, CA</td>
                                    <td>18.7</td>
                                    <td>Return to office</td>
                                    <td></td>
                                    <td>office</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-primary" onclick="downloadTemplate()">
                            <i class="bi bi-download"></i> Download Template
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function downloadTemplate() {
    const csvContent = `Date,Start,End,Mileage,Purpose,Notes,Tags
2024-01-15,"123 Main St, Anytown, CA","456 Client Ave, Business City, CA",25.3,"Client meeting","Quarterly review meeting","client, quarterly"
2024-01-16,"456 Client Ave, Business City, CA","789 Office Blvd, Workplace, CA",18.7,"Return to office","","office"`;
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'mileage_import_template.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// File input preview
document.getElementById('csv_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileName = file.name;
        const fileSize = (file.size / 1024).toFixed(1);
        console.log(`Selected file: ${fileName} (${fileSize} KB)`);
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/eddie/Development/mileage/resources/views/import/index.blade.php ENDPATH**/ ?>