<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name'
    ];

    /**
     * The events that are assigned.
     */
    protected $dispatchesEvents = [
        'created' => BadgeUnlocked::class
    ];

    /**
     * Get the user that owns the badge.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
