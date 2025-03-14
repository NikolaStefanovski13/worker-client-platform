<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'amount',
        'status',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
