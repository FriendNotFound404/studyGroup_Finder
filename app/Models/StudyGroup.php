<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyGroup extends Model
{
    protected $fillable = [
        'name', 'subject', 'level', 'description', 'creator_id', 'max_members'
    ];

    public function members()
    {
        return $this->hasMany(GroupMember::class, 'group_id');
    }

    public function messages()
    {
        return $this->hasMany(GroupMessage::class, 'group_id');
    }
}
