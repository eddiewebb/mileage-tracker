<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0">Mileage Rates</h1>
            <p class="text-muted">Configure IRS mileage rates by year</p>
        </div>
    </div>

    <!-- Information Alert -->
    <div class="row mb-4">
        <div class="col">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Important:</strong> The mileage rate must be consistent across all trips within a calendar year. 
                Updating the rate will apply to all trips in that year but will not affect other years.
            </div>
        </div>
    </div>

    <!-- Rates Table -->
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Annual Mileage Rates</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>Rate (¢/mile)</th>
                                    <th>Rate ($/mile)</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $rates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($rate->year); ?></strong>
                                            <?php if($rate->year == date('Y')): ?>
                                                <span class="badge bg-primary ms-2">Current</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="editable-rate" 
                                                  data-year="<?php echo e($rate->year); ?>" 
                                                  data-rate="<?php echo e($rate->rate_cents_per_mile); ?>">
                                                <?php echo e($rate->rate_cents_per_mile); ?>¢
                                            </span>
                                        </td>
                                        <td>$<?php echo e(number_format($rate->rate_in_dollars, 2)); ?></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary edit-rate-btn" 
                                                    data-year="<?php echo e($rate->year); ?>" 
                                                    data-rate="<?php echo e($rate->rate_cents_per_mile); ?>">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historical IRS Rates Reference -->
    <div class="row mt-4">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle"></i> Official IRS Rates Reference
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <strong>Recent IRS Rates:</strong><br>
                                2024: 67¢/mile<br>
                                2023: 65.5¢/mile<br>
                                2022: 62.5¢/mile<br>
                                2021: 56¢/mile
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">
                                <strong>Note:</strong> These rates are automatically set as defaults. 
                                You can adjust them if needed for your specific situation.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Rate Modal -->
<div class="modal fade" id="editRateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Mileage Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="edit-rate-form">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="edit-year" name="year">
                    
                    <div class="mb-3">
                        <label for="edit-rate" class="form-label">Rate (cents per mile)</label>
                        <div class="input-group">
                            <input type="number" 
                                   class="form-control" 
                                   id="edit-rate" 
                                   name="rate_cents_per_mile" 
                                   min="1" 
                                   max="1000" 
                                   step="0.5" 
                                   required>
                            <span class="input-group-text">¢</span>
                        </div>
                        <div class="form-text">
                            Enter the rate in cents per mile (e.g., 67 for $0.67/mile)
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-rate-btn">Save Rate</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('editRateModal'));
    
    // Handle edit button clicks
    document.querySelectorAll('.edit-rate-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const year = this.dataset.year;
            const rate = this.dataset.rate;
            
            document.getElementById('edit-year').value = year;
            document.getElementById('edit-rate').value = rate;
            document.querySelector('#editRateModal .modal-title').textContent = `Edit ${year} Mileage Rate`;
            
            editModal.show();
        });
    });
    
    // Handle save button
    document.getElementById('save-rate-btn').addEventListener('click', function() {
        const form = document.getElementById('edit-rate-form');
        const formData = new FormData(form);
        
        // Disable save button during request
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        
        fetch('<?php echo e(route("mileage-rates.update")); ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the display
                const year = formData.get('year');
                const newRate = formData.get('rate_cents_per_mile');
                const rateDisplay = document.querySelector(`[data-year="${year}"]`);
                
                if (rateDisplay) {
                    rateDisplay.textContent = newRate + '¢';
                    rateDisplay.dataset.rate = newRate;
                }
                
                // Update the edit button
                const editBtn = document.querySelector(`.edit-rate-btn[data-year="${year}"]`);
                if (editBtn) {
                    editBtn.dataset.rate = newRate;
                }
                
                // Show success message
                showAlert('success', data.message);
                
                // Close modal
                editModal.hide();
                
                // Refresh the page to update dollar amounts
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showAlert('danger', 'Failed to update mileage rate.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while updating the rate.');
        })
        .finally(() => {
            // Re-enable save button
            this.disabled = false;
            this.innerHTML = 'Save Rate';
        });
    });
});

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the container
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/eddie/Development/mileage/resources/views/mileage-rates/index.blade.php ENDPATH**/ ?>