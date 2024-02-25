<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'Type',
        'date',
        'user_id',
        'rest_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
