<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'title',
        'occurrence',
        'description',
        'user_id',
    ];

    protected $casts = [
        'occurrence' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
