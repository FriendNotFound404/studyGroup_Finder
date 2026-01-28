<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\StudyGroup;
use App\Models\GroupMessage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'school',
        'major',
        'avatar',
        'bio',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ✅ USER → STUDY GROUPS
    public function studyGroups()
    {
        return $this->belongsToMany(
            StudyGroup::class,
            'group_members',
            'user_id',
            'group_id'
        )->withPivot('role')
        ->withTimestamps();
    }

    // ✅ USER → GROUP MESSAGES
    public function messages()
    {
        return $this->hasMany(GroupMessage::class);
    }
}

