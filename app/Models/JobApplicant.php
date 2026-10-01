<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'position_applied',
        'subject',
        'message',
        'cv_path',
        'cv_filename',
        'email_uid',
        'email_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'email_date' => 'datetime',
    ];
}
