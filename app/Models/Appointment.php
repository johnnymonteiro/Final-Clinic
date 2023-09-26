<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Appointment extends Model
{
    use HasFactory;

    protected $guarded = [];  //tudo o que vier da interface vai ser guardado na base de dados


    /*
     *  Get the doctor that owns the appointment
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
