<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'color',
    ];

    /**
     * Get the user that owns the label.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the trips that use this label.
     */
    public function trips()
    {
        return $this->belongsToMany(Trip::class, 'trip_labels');
    }

    /**
     * Find or create a label for a user.
     */
    public static function findOrCreateForUser($userId, $labelName)
    {
        return static::firstOrCreate(
            [
                'user_id' => $userId,
                'name' => $labelName,
            ],
            [
                'color' => self::generateRandomColor(),
            ]
        );
    }

    /**
     * Generate a random color for new labels.
     */
    private static function generateRandomColor()
    {
        $colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FECA57',
            '#FF9FF3', '#54A0FF', '#5F27CD', '#00D2D3', '#FF9F43',
            '#10AC84', '#EE5A24', '#0ABDE3', '#C44569', '#FD79A8'
        ];
        
        return $colors[array_rand($colors)];
    }
}
