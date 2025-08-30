<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Trip;
use App\Models\MileageRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        
        // Create mileage rate for calculations
        MileageRate::create([
            'user_id' => $this->user->id,
            'year' => 2024,
            'rate_cents_per_mile' => 67,
        ]);
    }

    /**
     * Test reports page is accessible to authenticated users.
     */
    public function test_reports_page_is_accessible_to_authenticated_users()
    {
        $response = $this->actingAs($this->user)->get('/reports');

        $response->assertStatus(200);
        $response->assertSee('Trip Reports');
    }

    /**
     * Test CSV report generation with valid date range.
     */
    public function test_csv_report_generation_with_valid_date_range()
    {
        // Create test trips
        Trip::factory()->create([
            'user_id' => $this->user->id,
            'trip_date' => '2024-01-15',
            'mileage' => 25.5,
            'start_location' => 'Start Location',
            'end_location' => 'End Location',
        ]);

        $response = $this->actingAs($this->user)->post('/reports/generate', [
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'format' => 'csv',
        ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Start Location', $response->getContent());
    }

    /**
     * Test PDF report generation with valid date range.
     */
    public function test_pdf_report_generation_with_valid_date_range()
    {
        // Create test trips
        Trip::factory()->create([
            'user_id' => $this->user->id,
            'trip_date' => '2024-01-15',
            'mileage' => 25.5,
        ]);

        $response = $this->actingAs($this->user)->post('/reports/generate', [
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'format' => 'pdf',
        ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test report validation requires valid date range.
     */
    public function test_report_validation_requires_valid_date_range()
    {
        $response = $this->actingAs($this->user)->post('/reports/generate', [
            'start_date' => '2024-01-31',
            'end_date' => '2024-01-01', // End date before start date
            'format' => 'pdf',
        ]);

        $response->assertSessionHasErrors('end_date');
    }

    /**
     * Test report validation requires format selection.
     */
    public function test_report_validation_requires_format_selection()
    {
        $response = $this->actingAs($this->user)->post('/reports/generate', [
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            // Missing format
        ]);

        $response->assertSessionHasErrors('format');
    }

    /**
     * Test report only includes user's trips within date range.
     */
    public function test_report_only_includes_users_trips_within_date_range()
    {
        // Create user's trip within range
        $userTrip = Trip::factory()->create([
            'user_id' => $this->user->id,
            'trip_date' => '2024-01-15',
            'start_location' => 'User Trip Location',
        ]);

        // Create user's trip outside range
        Trip::factory()->create([
            'user_id' => $this->user->id,
            'trip_date' => '2024-02-15',
            'start_location' => 'Outside Range Location',
        ]);

        // Create other user's trip within range
        $otherUser = User::factory()->create();
        Trip::factory()->create([
            'user_id' => $otherUser->id,
            'trip_date' => '2024-01-15',
            'start_location' => 'Other User Location',
        ]);

        $response = $this->actingAs($this->user)->post('/reports/generate', [
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'format' => 'csv',
        ]);

        $content = $response->getContent();
        $this->assertStringContainsString('User Trip Location', $content);
        $this->assertStringNotContainsString('Outside Range Location', $content);
        $this->assertStringNotContainsString('Other User Location', $content);
    }

    /**
     * Test report includes IRS cost calculations.
     */
    public function test_report_includes_irs_cost_calculations()
    {
        Trip::factory()->create([
            'user_id' => $this->user->id,
            'trip_date' => '2024-01-15',
            'mileage' => 100, // 100 miles at $0.67/mile = $67.00
        ]);

        $response = $this->actingAs($this->user)->post('/reports/generate', [
            'start_date' => '2024-01-01',
            'end_date' => '2024-01-31',
            'format' => 'csv',
        ]);

        $this->assertStringContainsString('67.00', $response->getContent());
    }
}
