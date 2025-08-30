<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Trip;
use App\Models\Label;
use App\Models\MileageRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class TripTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test authenticated user can create a trip.
     */
    public function test_authenticated_user_can_create_trip()
    {
        $tripData = [
            'start_location' => '123 Main St, Anytown, CA',
            'end_location' => '456 Business Ave, Commerce City, CA',
            'mileage' => 25.5,
            'purpose' => 'Client meeting',
            'notes' => 'Quarterly review with important client',
            'trip_date' => '2024-01-15',
            'trip_time' => '14:30',
            'labels' => ['client', 'quarterly'],
        ];

        $response = $this->actingAs($this->user)->post('/trips', $tripData);

        $response->assertRedirect('/trips');
        $this->assertDatabaseHas('trips', [
            'user_id' => $this->user->id,
            'start_location' => $tripData['start_location'],
            'end_location' => $tripData['end_location'],
            'mileage' => $tripData['mileage'],
            'purpose' => $tripData['purpose'],
        ]);
    }

    /**
     * Test trip creation with labels creates new labels automatically.
     */
    public function test_trip_creation_with_new_labels_creates_labels_automatically()
    {
        $tripData = [
            'start_location' => '123 Main St, Anytown, CA',
            'end_location' => '456 Business Ave, Commerce City, CA',
            'mileage' => 25.5,
            'trip_date' => '2024-01-15',
            'trip_time' => '14:30',
            'labels' => ['new-label', 'another-new-label'],
        ];

        $this->actingAs($this->user)->post('/trips', $tripData);

        $this->assertDatabaseHas('labels', [
            'user_id' => $this->user->id,
            'name' => 'new-label',
        ]);

        $this->assertDatabaseHas('labels', [
            'user_id' => $this->user->id,
            'name' => 'another-new-label',
        ]);
    }

    /**
     * Test trip validation requires required fields.
     */
    public function test_trip_creation_requires_required_fields()
    {
        $response = $this->actingAs($this->user)->post('/trips', []);

        $response->assertSessionHasErrors([
            'start_location',
            'end_location',
            'mileage',
            'trip_date',
            'trip_time',
        ]);
    }

    /**
     * Test user can only access their own trips.
     */
    public function test_user_can_only_access_own_trips()
    {
        $otherUser = User::factory()->create();
        $otherTrip = Trip::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get("/trips/{$otherTrip->id}");

        $response->assertStatus(403);
    }

    /**
     * Test trip IRS cost calculation.
     */
    public function test_trip_irs_cost_calculation()
    {
        // Create mileage rate for 2024
        MileageRate::create([
            'user_id' => $this->user->id,
            'year' => 2024,
            'rate_cents_per_mile' => 67, // $0.67 per mile
        ]);

        $trip = Trip::factory()->create([
            'user_id' => $this->user->id,
            'mileage' => 100,
            'trip_date' => '2024-01-15',
        ]);

        $expectedCost = 100 * 0.67; // 100 miles * $0.67/mile = $67.00
        $this->assertEquals($expectedCost, $trip->irs_rated_cost);
    }

    /**
     * Test trip can be updated by owner.
     */
    public function test_trip_can_be_updated_by_owner()
    {
        $trip = Trip::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'start_location' => 'Updated Start Location',
            'end_location' => 'Updated End Location',
            'mileage' => 50.0,
            'purpose' => 'Updated purpose',
            'notes' => 'Updated notes',
            'trip_date' => '2024-02-01',
            'trip_time' => '10:00',
        ];

        $response = $this->actingAs($this->user)->put("/trips/{$trip->id}", $updateData);

        $response->assertRedirect("/trips/{$trip->id}");
        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'start_location' => $updateData['start_location'],
            'end_location' => $updateData['end_location'],
            'mileage' => $updateData['mileage'],
        ]);
    }

    /**
     * Test trip can be deleted by owner.
     */
    public function test_trip_can_be_deleted_by_owner()
    {
        $trip = Trip::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete("/trips/{$trip->id}");

        $response->assertRedirect('/trips');
        $this->assertDatabaseMissing('trips', ['id' => $trip->id]);
    }

    /**
     * Test trips index shows only user's trips.
     */
    public function test_trips_index_shows_only_users_trips()
    {
        $userTrip = Trip::factory()->create(['user_id' => $this->user->id]);
        $otherUser = User::factory()->create();
        $otherTrip = Trip::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get('/trips');

        $response->assertStatus(200);
        $response->assertSee($userTrip->start_location);
        $response->assertDontSee($otherTrip->start_location);
    }
}
