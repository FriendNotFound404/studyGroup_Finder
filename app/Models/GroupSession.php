<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSession extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'group_id',
        'title',
        'start_time',
        'end_time',
        'meeting_link',
        'created_by'
    ];
}