<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\MileageRate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Dompdf\Dompdf;
use League\Csv\Writer;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the reports form.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Generate and download report.
     */
    public function generate(Request $request)
    {
        $this->validateReportRequest($request);

        $trips = $this->getTripsInDateRange($request);
        $reportData = $this->prepareReportData($trips);

        if ($request->format === 'pdf') {
            return $this->generatePdfReport($reportData, $request);
        } else {
            return $this->generateCsvReport($reportData, $request);
        }
    }

    /**
     * Validate report generation request.
     */
    private function validateReportRequest(Request $request)
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'format' => ['required', 'in:pdf,csv'],
        ]);
    }

    /**
     * Get trips within the specified date range.
     */
    private function getTripsInDateRange(Request $request)
    {
        return auth()->user()->trips()
            ->with('labels')
            ->inDateRange($request->start_date, $request->end_date)
            ->orderBy('trip_date')
            ->orderBy('trip_time')
            ->get();
    }

    /**
     * Prepare report data with IRS calculations.
     */
    private function prepareReportData($trips)
    {
        $reportData = [];
        $totalMileage = 0;
        $totalCost = 0;

        foreach ($trips as $trip) {
            $irsRatedCost = $trip->irs_rated_cost;
            $totalMileage += $trip->mileage;
            $totalCost += $irsRatedCost;

            $reportData[] = [
                'date' => $trip->trip_date->format('Y-m-d'),
                'time' => $trip->trip_time->format('H:i'),
                'start_location' => $trip->start_location,
                'end_location' => $trip->end_location,
                'mileage' => number_format($trip->mileage, 2),
                'purpose' => $trip->purpose,
                'notes' => $trip->notes,
                'labels' => $trip->labels->pluck('name')->join(', '),
                'irs_cost' => number_format($irsRatedCost, 2),
            ];
        }

        return [
            'trips' => $reportData,
            'summary' => [
                'total_trips' => count($reportData),
                'total_mileage' => number_format($totalMileage, 2),
                'total_cost' => number_format($totalCost, 2),
            ]
        ];
    }

    /**
     * Generate PDF report.
     */
    private function generatePdfReport($reportData, Request $request)
    {
        $html = view('reports.pdf', [
            'reportData' => $reportData,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'user' => auth()->user(),
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'mileage-report-' . $request->start_date . '-to-' . $request->end_date . '.pdf';

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generate CSV report.
     */
    private function generateCsvReport($reportData, Request $request)
    {
        $csv = Writer::createFromString('');
        
        // Add headers
        $csv->insertOne([
            'Date', 'Time', 'Start Location', 'End Location', 
            'Mileage', 'Purpose', 'Notes', 'Labels', 'IRS Cost ($)'
        ]);

        // Add data rows
        foreach ($reportData['trips'] as $trip) {
            $csv->insertOne([
                $trip['date'],
                $trip['time'],
                $trip['start_location'],
                $trip['end_location'],
                $trip['mileage'],
                $trip['purpose'],
                $trip['notes'],
                $trip['labels'],
                $trip['irs_cost'],
            ]);
        }

        $filename = 'mileage-report-' . $request->start_date . '-to-' . $request->end_date . '.csv';

        return response($csv->toString(), 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
