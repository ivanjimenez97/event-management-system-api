<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'event_id'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
