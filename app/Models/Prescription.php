<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use HasFactory;

    protected $guarded = [];


    /**
     * Get the doctor that owns the prescription.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user that owns the prescription
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
