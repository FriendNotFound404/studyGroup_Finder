<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'description',
        'status',
        'max_members',
        'creator_id',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members()
    {
        return $this->belongsToMany(
            User::class,
            'group_members',
            'group_id',
            'user_id'
        )->withPivot('role')
        ->withTimestamps();
    }

    public function sessions()
    {
        return $this->hasMany(GroupSession::class);
    }

    public function messages()
    {
        return $this->hasMany(GroupMessage::class, 'group_id');
    }
}
