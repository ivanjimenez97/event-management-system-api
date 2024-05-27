<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasedTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_id'
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship to retrieve the event data
    public function event(): BelongsTo
    {
        // Assuming the 'event_id' column exists in the 'tickets' table
        return $this->belongsTo(Event::class);
    }
}
