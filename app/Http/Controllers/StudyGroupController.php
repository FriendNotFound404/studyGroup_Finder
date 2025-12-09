<?php

namespace App\Http\Controllers;

use App\Models\StudyGroup;
use App\Models\GroupMember;
use Illuminate\Http\Request;

class StudyGroupController extends Controller
{
    // Create a new group
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'subject' => 'required',
        ]);

        $group = StudyGroup::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'level' => $request->level,
            'description' => $request->description,
            'creator_id' => auth()->id,
            'max_members' => $request->max_members ?? 20,
        ]);

        // Add creator as first member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => auth()->id,
        ]);

        return response()->json($group);
    }

    // Join a group
    public function join($id)
    {
        $group = StudyGroup::findOrFail($id);

        if ($group->members()->count() >= $group->max_members) {
            return response()->json(['error' => 'Group is full'], 400);
        }

        GroupMember::firstOrCreate([
            'group_id' => $id,
            'user_id' => auth()->id,
        ]);

        return response()->json(['message' => 'Joined group']);
    }

    // Leave group
    public function leave($id)
    {
        GroupMember::where([
            'group_id' => $id,
            'user_id' => auth()->id,
        ])->delete();

        return response()->json(['message' => 'Left group']);
    }

    // Search groups
    public function search(Request $request)
    {
        $q = $request->q;

        $groups = StudyGroup::where('name', 'LIKE', "%$q%")
            ->orWhere('subject', 'LIKE', "%$q%")
            ->orWhere('level', 'LIKE', "%$q%")
            ->get();

        return response()->json($groups);
    }

    // List all groups
    public function index()
    {
        return StudyGroup::with('members')->get();
    }
}

