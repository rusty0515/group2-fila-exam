<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_initial',
        'last_name',
       
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
