<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Task;
use App\Models\Report;
use App\Models\Absence;
use App\Models\Station;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'Job_title',
        'roles',
        'password',
    ];
    protected $hidden = [
        'password',
        'email_verified_at',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    function stations()
    {
        return $this->belongsToMany(Station::class, 'user_stations');
    }
    function tasks()
    {
        return $this->hasMany(Task::class);
    }
    function reports()
    {
        return $this->hasMany(Report::class);
    }
    function absences()
    {
        return $this->hasMany(Absence::class);
    }
    function regularBalance()
    {
        return $this->hasOne(RegularBalance::class);
    }
    function restBalance()
    {
        return $this->hasOne(RestBalance::class);
    }
    function restallowance()
    {
        return $this->hasOne(Restallowance::class);
    }
}
