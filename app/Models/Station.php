<?php

namespace App\Models;

use App\Models\Post;
use App\Models\Task;
use App\Models\User;
use App\Models\Report;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Station extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_stations');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}