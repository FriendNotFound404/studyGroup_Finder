<?php

namespace App\Http\Controllers;

use App\Models\StudyGroup;
use App\Models\GroupJoinRequest;
use Illuminate\Http\Request;

class GroupJoinRequestController extends Controller
{
    /**
     * POST /api/groups/{id}/join-request
     */
    public function requestJoin(Request $request, $id)
    {
        $user = $request->user();
        $group = StudyGroup::findOrFail($id);

        if ($group->status !== 'open') {
            return response()->json(['message' => 'Group is closed'], 403);
        }

        GroupJoinRequest::firstOrCreate([
            'group_id' => $group->id,
            'user_id'  => $user->id,
        ], [
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Join request sent']);
    }

    /**
     * POST /api/join-requests/{id}/approve
     */
    public function approve(Request $request, $requestId)
    {
        $user = $request->user();
        $joinRequest = GroupJoinRequest::findOrFail($requestId);
        $group = $joinRequest->group;

        // ✅ leader check
        if (! $group->members()
            ->wherePivot('role', 'leader')
            ->where('users.id', $user->id)
            ->exists()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // ✅ capacity check
        if ($group->max_members &&
            $group->members()->count() >= $group->max_members) {
            return response()->json(['message' => 'Group is full'], 409);
        }

        // add member
        $group->members()->syncWithoutDetaching([
            $joinRequest->user_id => ['role' => 'member']
        ]);

        $joinRequest->update(['status' => 'approved']);

        return response()->json(['message' => 'User added']);
    }
}