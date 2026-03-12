<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpdeskArticle extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'question',
        'answer',
    ];
}
