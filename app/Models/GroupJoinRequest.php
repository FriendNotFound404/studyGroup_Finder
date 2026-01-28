<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupJoinRequest extends Model
{
    use HasFactory;
    
    protected $fillable = ['group_id', 'user_id', 'status'];

    public function group()
    {
        return $this->belongsTo(StudyGroup::class);
    }
}