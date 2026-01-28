<?php

namespace App\Http\Controllers;

use App\Models\StudyGroup;
use Illuminate\Http\Request;

class StudyGroupController extends Controller
{
    /**
     * GET /api/groups
     * Search & filter groups
     */
    public function index(Request $request)
    {
        $groups = StudyGroup::query()
            ->when($request->subject, fn ($q) =>
                $q->where('subject', 'like', "%{$request->subject}%")
            )
            ->when($request->status, fn ($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->search, fn ($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
            );
        return response()->json($groups);
    }

    /**
     * POST /api/groups
     * Create study group
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'subject'     => 'required|string|max:255',
            'level'       => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'max_members' => 'nullable|integer|min:1',
        ]);

        $group = StudyGroup::create([
            ...$data,
            'creator_id' => $request->user()->id,
            'status'     => 'open',
        ]);

        // creator auto-joins
        $group->members()->attach($request->user()->id);

        return response()->json($group, 201);
    }

    /**
     * GET /api/groups/{id}
     */
    public function show($id)
    {
        $group = StudyGroup::with('members:id,name')
            ->findOrFail($id);

        return response()->json($group);
    }

    /**
     * PATCH /api/groups/{id}
     * Update group (creator only)
     */
    public function update(Request $request, $id)
    {
        $group = StudyGroup::findOrFail($id);

        abort_unless($group->creator_id === $request->user()->id, 403);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'subject'     => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'in:open,closed,archived',
            'max_members' => 'nullable|integer|min:1',
        ]);

        $group->update($data);

        return response()->json($group);
    }

    /**
     * DELETE /api/groups/{id}
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user(); // ✅ guaranteed by auth:sanctum

        $group = StudyGroup::findOrFail($id);

        abort_unless($group->creator_id === $user->id, 403);

        $group->delete();

        return response()->json(['message' => 'Group deleted']);
    }

    /**
     * POST /api/groups/{id}/join
     */
    public function join(Request $request, $id)
    {
        $user = $request->user();

        $group = StudyGroup::findOrFail($id);

        if ($group->status !== 'open') {
            return response()->json(['message' => 'Group is closed'], 403);
        }

        if ($group->max_members && $group->members()->count() >= $group->max_members) {
            return response()->json(['message' => 'Group is full'], 403);
        }

        $group->members()->syncWithoutDetaching($user->id);

        return response()->json(['message' => 'Joined group']);
    }

    /**
     * POST /api/groups/{id}/leave
     */
    public function leave(Request $request, $id)
    {
        $user = $request->user();

        $group = StudyGroup::findOrFail($id);

        $group->members()->detach($user->id);

        return response()->json(['message' => 'Left group']);
    }
}