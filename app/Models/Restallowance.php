<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restallowance extends Model
{
    use HasFactory;
    protected $fillable = ['description', 'date', 'state', 'user_id'];
    function user()
    {
        return $this->belongsTo(User::class);
    }
}
