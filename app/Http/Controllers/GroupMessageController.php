<?php

namespace App\Http\Controllers;

use App\Models\GroupMessage;
use Illuminate\Http\Request;

class GroupMessageController extends Controller
{
    // Get messages for a group
    public function index($groupId)
    {
        return GroupMessage::where('group_id', $groupId)
            ->with('user:id,name')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    // Send message
    public function send(Request $request, $groupId)
    {
        $request->validate(['message' => 'required']);

        $msg = GroupMessage::create([
            'group_id' => $groupId,
            'user_id' => auth()->id,
            'message' => $request->message,
        ]);

        return response()->json($msg);
    }
}

