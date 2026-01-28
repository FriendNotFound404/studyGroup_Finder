<?php

namespace App\Http\Controllers;

use App\Models\GroupMessage;
use App\Models\StudyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupMessageController extends Controller
{
    // Get messages for a group
    public function index(Request $request, $groupId)
    {
        // Validate that the group exists
        $group = StudyGroup::find($groupId);
        
        if (!$group) {
            return response()->json([
                'message' => 'Group not found'
            ], 404);
        }
        
        // Check if user is a member of the group (optional security)
        if (!$group->members()->where('user_id', Auth::id())->exists()) {
            return response()->json([
                'message' => 'You are not a member of this group'
            ], 403);
        }
        
        // Pagination support (optional but recommended)
        $perPage = $request->get('per_page', 50);
        
        $messages = GroupMessage::where('group_id', $groupId)
            ->with(['user:id,name,profile_image']) // Add more user fields if needed
            ->orderBy('created_at', 'asc')
            ->paginate($perPage);
            
        return response()->json($messages);
    }

    // Send message
    public function send(Request $request, $groupId)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);
        
        // Validate group exists and user is a member
        $group = StudyGroup::find($groupId);
        
        if (!$group) {
            return response()->json([
                'message' => 'Group not found'
            ], 404);
        }
        
        // Check if user is a member
        if (!$group->members()->where('user_id', Auth::id())->exists()) {
            return response()->json([
                'message' => 'You are not a member of this group'
            ], 403);
        }
        
        // Create message
        $msg = GroupMessage::create([
            'group_id' => $groupId,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);
        
        // Load relationships for response
        $msg->load('user:id,name');
        
        // Real-time broadcasting (optional - for WebSocket)
        // broadcast(new NewMessageEvent($msg))->toOthers();
        
        return response()->json([
            'message' => $msg,
            'success' => true
        ], 201);
    }
    
    // Optional: Update message
    public function update(Request $request, $groupId, $messageId)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);
        
        $msg = GroupMessage::where('group_id', $groupId)
            ->where('id', $messageId)
            ->where('user_id', Auth::id())
            ->first();
            
        if (!$msg) {
            return response()->json([
                'message' => 'Message not found or unauthorized'
            ], 404);
        }
        
        // Check if message is not too old to edit (e.g., 15 minutes)
        if ($msg->created_at->diffInMinutes(now()) > 15) {
            return response()->json([
                'message' => 'Message can only be edited within 15 minutes'
            ], 403);
        }
        
        $msg->update([
            'message' => $request->message,
            'edited' => true,
        ]);
        
        return response()->json($msg);
    }
    
    // Optional: Delete message
    public function destroy($groupId, $messageId)
    {
        $msg = GroupMessage::where('group_id', $groupId)
            ->where('id', $messageId)
            ->where('user_id', Auth::id())
            ->first();
            
        if (!$msg) {
            return response()->json([
                'message' => 'Message not found or unauthorized'
            ], 404);
        }
        
        $msg->delete();
        
        return response()->json([
            'message' => 'Message deleted successfully'
        ]);
    }
}

