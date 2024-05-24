<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    protected $guarded = [];

    //Since We don't have updated_at column We can override updated_at column field.
    const UPDATED_AT = null;
}
