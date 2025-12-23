<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\StudyGroup;
use App\Models\GroupMessage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
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
        )->withTimestamps();
    }

    // ✅ USER → GROUP MESSAGES
    public function messages()
    {
        return $this->hasMany(GroupMessage::class);
    }
}

