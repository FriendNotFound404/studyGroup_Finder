<?php
use App\Models\StudyGroup;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('group.{groupId}', function ($user, $groupId) {
    return StudyGroup::where('id', $groupId)
        ->whereHas('members', fn ($q) => $q->where('user_id', $user->id))
        ->exists();
});