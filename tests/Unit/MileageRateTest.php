<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\MileageRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MileageRateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test mileage rate creation for user and year.
     */
    public function test_mileage_rate_creation_for_user_and_year()
    {
        $user = User::factory()->create();
        
        $rate = MileageRate::getOrCreateForUserAndYear($user->id, 2024);

        $this->assertInstanceOf(MileageRate::class, $rate);
        $this->assertEquals($user->id, $rate->user_id);
        $this->assertEquals(2024, $rate->year);
        $this->assertEquals(67, $rate->rate_cents_per_mile); // 2024 default
    }

    /**
     * Test existing rate is returned for user and year.
     */
    public function test_existing_rate_is_returned_for_user_and_year()
    {
        $user = User::factory()->create();
        
        // Create existing rate
        $existingRate = MileageRate::create([
            'user_id' => $user->id,
            'year' => 2024,
            'rate_cents_per_mile' => 75,
        ]);

        $rate = MileageRate::getOrCreateForUserAndYear($user->id, 2024);

        $this->assertEquals($existingRate->id, $rate->id);
        $this->assertEquals(75, $rate->rate_cents_per_mile);
    }

    /**
     * Test rate in dollars conversion.
     */
    public function test_rate_in_dollars_conversion()
    {
        $rate = new MileageRate([
            'rate_cents_per_mile' => 67,
        ]);

        $this->assertEquals(0.67, $rate->rate_in_dollars);
    }

    /**
     * Test default IRS rates for different years.
     */
    public function test_default_irs_rates_for_different_years()
    {
        $user = User::factory()->create();

        $rate2024 = MileageRate::getOrCreateForUserAndYear($user->id, 2024);
        $rate2023 = MileageRate::getOrCreateForUserAndYear($user->id, 2023);
        $rate2022 = MileageRate::getOrCreateForUserAndYear($user->id, 2022);

        $this->assertEquals(67, $rate2024->rate_cents_per_mile);
        $this->assertEquals(66, $rate2023->rate_cents_per_mile); // Rounded for integer storage
        $this->assertEquals(63, $rate2022->rate_cents_per_mile); // Rounded for integer storage
    }

    /**
     * Test unknown year defaults to current rate.
     */
    public function test_unknown_year_defaults_to_current_rate()
    {
        $user = User::factory()->create();
        
        $rate = MileageRate::getOrCreateForUserAndYear($user->id, 2030); // Future year

        $this->assertEquals(67, $rate->rate_cents_per_mile); // Should default to 2024 rate
    }

    /**
     * Test mileage rate relationships.
     */
    public function test_mileage_rate_relationships()
    {
        $user = User::factory()->create();
        $rate = MileageRate::create([
            'user_id' => $user->id,
            'year' => 2024,
            'rate_cents_per_mile' => 67,
        ]);

        $this->assertEquals($user->id, $rate->user->id);
        $this->assertTrue($user->mileageRates->contains($rate));
    }
}
