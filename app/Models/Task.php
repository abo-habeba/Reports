<?php

namespace App\Models;

use App\Models\Station;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'ppm',
        'quantity',
        'cont_hours',
        'actual_time',
        'slices_ton',
        'shift',
        'name',
        'description',
        'station_id',
        'preparation_id',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function station(){
        return $this->belongsTo(Station::class);
    }
    public function preparation(){
        return $this->belongsTo(preparations::class);
    }

}