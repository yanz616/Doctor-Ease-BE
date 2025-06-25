<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable =[
        'name',
        'specialization',
        'photo_url'
    ];

    public function appointments() {
        return $this->hasMany(Appointment::class);
    }
    
}
