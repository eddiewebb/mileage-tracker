<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MileageRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MileageRateTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test mileage rate can be updated for specific year.
     */
    public function test_mileage_rate_can_be_updated_for_specific_year()
    {
        $response = $this->actingAs($this->user)->post('/mileage-rates', [
            'year' => 2024,
            'rate_cents_per_mile' => 70,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('mileage_rates', [
            'user_id' => $this->user->id,
            'year' => 2024,
            'rate_cents_per_mile' => 70,
        ]);
    }

    /**
     * Test mileage rate validation requires valid year.
     */
    public function test_mileage_rate_validation_requires_valid_year()
    {
        $response = $this->actingAs($this->user)->post('/mileage-rates', [
            'year' => 1999, // Too old
            'rate_cents_per_mile' => 70,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('year');
    }

    /**
     * Test mileage rate validation requires valid rate.
     */
    public function test_mileage_rate_validation_requires_valid_rate()
    {
        $response = $this->actingAs($this->user)->post('/mileage-rates', [
            'year' => 2024,
            'rate_cents_per_mile' => 0, // Invalid rate
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('rate_cents_per_mile');
    }

    /**
     * Test default IRS rates are created automatically.
     */
    public function test_default_irs_rates_are_created_automatically()
    {
        $rate = MileageRate::getOrCreateForUserAndYear($this->user->id, 2024);

        $this->assertEquals(67, $rate->rate_cents_per_mile); // 2024 default rate
        $this->assertEquals(2024, $rate->year);
        $this->assertEquals($this->user->id, $rate->user_id);
    }

    /**
     * Test existing rate is returned instead of creating duplicate.
     */
    public function test_existing_rate_is_returned_instead_of_creating_duplicate()
    {
        // Create initial rate
        $existingRate = MileageRate::create([
            'user_id' => $this->user->id,
            'year' => 2024,
            'rate_cents_per_mile' => 75,
        ]);

        // Try to get or create the same rate
        $rate = MileageRate::getOrCreateForUserAndYear($this->user->id, 2024);

        $this->assertEquals($existingRate->id, $rate->id);
        $this->assertEquals(75, $rate->rate_cents_per_mile); // Should keep existing rate
    }

    /**
     * Test rate in dollars calculation.
     */
    public function test_rate_in_dollars_calculation()
    {
        $rate = MileageRate::create([
            'user_id' => $this->user->id,
            'year' => 2024,
            'rate_cents_per_mile' => 67,
        ]);

        $this->assertEquals(0.67, $rate->rate_in_dollars);
    }

    /**
     * Test each user has separate mileage rates.
     */
    public function test_each_user_has_separate_mileage_rates()
    {
        $otherUser = User::factory()->create();

        $userRate = MileageRate::getOrCreateForUserAndYear($this->user->id, 2024);
        $otherUserRate = MileageRate::getOrCreateForUserAndYear($otherUser->id, 2024);

        $this->assertEquals($this->user->id, $userRate->user_id);
        $this->assertEquals($otherUser->id, $otherUserRate->user_id);
        $this->assertNotEquals($userRate->id, $otherUserRate->id);
    }
}
