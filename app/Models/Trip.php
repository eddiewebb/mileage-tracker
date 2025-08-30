<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_location',
        'end_location',
        'mileage',
        'notes',
        'purpose',
        'trip_date',
        'trip_time',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
    ];

    protected $casts = [
        'trip_date' => 'date',
        'trip_time' => 'datetime',
        'mileage' => 'decimal:2',
        'start_latitude' => 'decimal:8',
        'start_longitude' => 'decimal:8',
        'end_latitude' => 'decimal:8',
        'end_longitude' => 'decimal:8',
    ];

    /**
     * Get the user that owns the trip.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the labels associated with this trip.
     */
    public function labels()
    {
        return $this->belongsToMany(Label::class, 'trip_labels');
    }

    /**
     * Get the IRS-rated cost for this trip.
     */
    public function getIrsRatedCostAttribute()
    {
        $year = $this->trip_date->year;
        $mileageRate = $this->user->mileageRates()
            ->where('year', $year)
            ->first();

        if (!$mileageRate) {
            return 0;
        }

        return $this->mileage * ($mileageRate->rate_cents_per_mile / 100);
    }

    /**
     * Scope trips within a date range.
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('trip_date', [$startDate, $endDate]);
    }
}
