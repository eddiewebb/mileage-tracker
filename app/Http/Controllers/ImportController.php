<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Label;
use Illuminate\Http\Request;
use League\Csv\Reader;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the import form.
     */
    public function index()
    {
        return view('import.index');
    }

    /**
     * Process the CSV upload and show preview.
     */
    public function preview(Request $request)
    {
        $this->validateCsvUpload($request);

        $csvData = $this->parseCsvFile($request->file('csv_file'));
        $preview = $this->preparePreviewData($csvData);

        return view('import.preview', compact('preview'));
    }

    /**
     * Confirm and import the reviewed trips.
     */
    public function confirm(Request $request)
    {
        $this->validateImportConfirmation($request);

        $importedTrips = $this->importTrips($request->trips);

        return redirect()->route('trips.index')
            ->with('success', "Successfully imported {$importedTrips} trips!");
    }

    /**
     * Validate CSV file upload.
     */
    private function validateCsvUpload(Request $request)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);
    }

    /**
     * Parse the uploaded CSV file.
     */
    private function parseCsvFile($file)
    {
        $csv = Reader::createFromPath($file->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        $records = [];
        foreach ($csv as $record) {
            $records[] = $this->mapCsvRecord($record);
        }

        return $records;
    }

    /**
     * Map CSV record to trip data structure.
     */
    private function mapCsvRecord($record)
    {
        return [
            'date' => $this->parseDate($record['date'] ?? $record['Date'] ?? ''),
            'start' => $record['start'] ?? $record['Start'] ?? $record['start_location'] ?? '',
            'end' => $record['end'] ?? $record['End'] ?? $record['end_location'] ?? '',
            'mileage' => $this->parseMileage($record['mileage'] ?? $record['Mileage'] ?? ''),
            'notes' => $record['notes'] ?? $record['Notes'] ?? '',
            'tags' => $this->parseTags($record['tags'] ?? $record['Tags'] ?? ''),
            'purpose' => $record['purpose'] ?? $record['Purpose'] ?? '',
        ];
    }

    /**
     * Prepare preview data for user review.
     */
    private function preparePreviewData($csvData)
    {
        $valid = [];
        $errors = [];

        foreach ($csvData as $index => $record) {
            $validation = $this->validateImportRecord($record, $index + 1);
            
            if ($validation['valid']) {
                $valid[] = $record;
            } else {
                $errors[] = $validation;
            }
        }

        return [
            'valid' => $valid,
            'errors' => $errors,
            'total_records' => count($csvData),
            'valid_count' => count($valid),
            'error_count' => count($errors),
        ];
    }

    /**
     * Import validated trips.
     */
    private function importTrips($trips)
    {
        $imported = 0;

        DB::transaction(function () use ($trips, &$imported) {
            foreach ($trips as $tripData) {
                $trip = $this->createTripFromImport($tripData);
                $this->attachImportLabels($trip, $tripData['tags'] ?? []);
                $imported++;
            }
        });

        return $imported;
    }

    /**
     * Helper methods for data parsing and validation
     */
    private function parseDate($dateString)
    {
        try {
            return Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseMileage($mileageString)
    {
        return is_numeric($mileageString) ? (float) $mileageString : null;
    }

    private function parseTags($tagsString)
    {
        if (empty($tagsString)) {
            return [];
        }
        
        return array_map('trim', explode(',', $tagsString));
    }

    private function validateImportRecord($record, $rowNumber)
    {
        $errors = [];

        if (!$record['date']) {
            $errors[] = 'Invalid date format';
        }

        if (empty($record['start'])) {
            $errors[] = 'Start location is required';
        }

        if (empty($record['end'])) {
            $errors[] = 'End location is required';
        }

        if (!$record['mileage'] || $record['mileage'] <= 0) {
            $errors[] = 'Valid mileage is required';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'row' => $rowNumber,
            'record' => $record,
        ];
    }

    private function createTripFromImport($tripData)
    {
        return auth()->user()->trips()->create([
            'start_location' => $tripData['start'],
            'end_location' => $tripData['end'],
            'mileage' => $tripData['mileage'],
            'purpose' => $tripData['purpose'],
            'notes' => $tripData['notes'],
            'trip_date' => $tripData['date'],
            'trip_time' => Carbon::createFromTime(12, 0), // Default to noon
        ]);
    }

    private function attachImportLabels($trip, $tags)
    {
        if (empty($tags)) {
            return;
        }

        $labelIds = [];
        foreach ($tags as $tagName) {
            $label = Label::findOrCreateForUser(auth()->id(), $tagName);
            $labelIds[] = $label->id;
        }

        $trip->labels()->attach($labelIds);
    }

    private function validateImportConfirmation(Request $request)
    {
        $request->validate([
            'trips' => ['required', 'array'],
            'trips.*.start' => ['required', 'string'],
            'trips.*.end' => ['required', 'string'],
            'trips.*.mileage' => ['required', 'numeric', 'min:0.01'],
            'trips.*.date' => ['required', 'date'],
            'trips.*.purpose' => ['nullable', 'string'],
            'trips.*.notes' => ['nullable', 'string'],
            'trips.*.tags' => ['nullable', 'array'],
            'trips.*.tags.*' => ['string', 'max:50'],
        ]);
    }
}
