<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mileage Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #007bff;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            text-align: center;
        }
        
        .summary-item h3 {
            margin: 0 0 5px 0;
            color: #007bff;
            font-size: 18px;
        }
        
        .summary-item p {
            margin: 0;
            color: #666;
            font-size: 11px;
        }
        
        .trips-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .trips-table th,
        .trips-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        
        .trips-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        
        .trips-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Business Mileage Report</h1>
        <p><strong><?php echo e($user->name); ?></strong> (<?php echo e($user->email); ?>)</p>
        <p>Report Period: <?php echo e(Carbon\Carbon::parse($startDate)->format('F j, Y')); ?> - <?php echo e(Carbon\Carbon::parse($endDate)->format('F j, Y')); ?></p>
        <p>Generated: <?php echo e(Carbon\Carbon::now()->format('F j, Y g:i A')); ?></p>
    </div>

    <!-- Summary -->
    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <h3><?php echo e($reportData['summary']['total_trips']); ?></h3>
                <p>Total Trips</p>
            </div>
            <div class="summary-item">
                <h3><?php echo e($reportData['summary']['total_mileage']); ?></h3>
                <p>Total Miles</p>
            </div>
            <div class="summary-item">
                <h3>$<?php echo e($reportData['summary']['total_cost']); ?></h3>
                <p>Total IRS Deduction</p>
            </div>
        </div>
    </div>

    <!-- Trips Table -->
    <?php if(count($reportData['trips']) > 0): ?>
        <table class="trips-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Start Location</th>
                    <th>End Location</th>
                    <th class="text-right">Miles</th>
                    <th>Purpose</th>
                    <th>Labels</th>
                    <th class="text-right">IRS Cost</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $reportData['trips']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($trip['date']); ?></td>
                        <td><?php echo e($trip['time']); ?></td>
                        <td><?php echo e($trip['start_location']); ?></td>
                        <td><?php echo e($trip['end_location']); ?></td>
                        <td class="text-right"><?php echo e($trip['mileage']); ?></td>
                        <td><?php echo e($trip['purpose']); ?></td>
                        <td><?php echo e($trip['labels']); ?></td>
                        <td class="text-right">$<?php echo e($trip['irs_cost']); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background-color: #e9ecef;">
                    <td colspan="4">TOTALS</td>
                    <td class="text-right"><?php echo e($reportData['summary']['total_mileage']); ?></td>
                    <td colspan="2"></td>
                    <td class="text-right">$<?php echo e($reportData['summary']['total_cost']); ?></td>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #666;">
            <p>No trips found for the selected date range.</p>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer">
        <p>This report was generated by <?php echo e(config('app.name')); ?> for tax and business record-keeping purposes.</p>
        <p>Please consult with a tax professional for proper documentation and filing requirements.</p>
    </div>
</body>
</html>
<?php /**PATH /Users/eddie/Development/mileage/resources/views/reports/pdf.blade.php ENDPATH**/ ?>