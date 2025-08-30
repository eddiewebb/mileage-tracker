<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MileageRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'rate_cents_per_mile',
    ];

    protected $casts = [
        'year' => 'integer',
        'rate_cents_per_mile' => 'integer',
    ];

    /**
     * Get the user that owns the mileage rate.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the rate in dollars.
     */
    public function getRateInDollarsAttribute()
    {
        return $this->rate_cents_per_mile / 100;
    }

    /**
     * Get or create the mileage rate for a user and year.
     */
    public static function getOrCreateForUserAndYear($userId, $year)
    {
        $existing = static::where('user_id', $userId)
            ->where('year', $year)
            ->first();

        if ($existing) {
            return $existing;
        }

        // Get default IRS rate for the year
        $defaultRate = self::getDefaultIrsRate($year);

        return static::create([
            'user_id' => $userId,
            'year' => $year,
            'rate_cents_per_mile' => $defaultRate,
        ]);
    }

    /**
     * Get the default IRS rate for a given year.
     */
    private static function getDefaultIrsRate($year)
    {
        // IRS rates by year (in cents per mile)
        $irsRates = [
            2024 => 67,
            2023 => 66, // Rounded for integer storage
            2022 => 63, // Rounded for integer storage  
            2021 => 56,
            2020 => 58, // Rounded for integer storage
            2019 => 58,
        ];

        return $irsRates[$year] ?? 67; // Default to 2024 rate if year not found
    }
}
