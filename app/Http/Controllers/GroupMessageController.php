<?php

namespace App\Http\Controllers;

use App\Models\GroupMessage;
use App\Models\StudyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\GroupMessageSent;

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

    public function send(Request $request, $groupId)
    {
        $data = $request->validate([
            'message' => 'nullable|string|max:2000',
            'file' => 'nullable|file|max:10240',
        ]);

        $user = $request->user();

        $group = StudyGroup::findOrFail($groupId);

        if (! $group->members()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'You are not a member of this group'
            ], 403);
        }

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('group_files', 'public');
            $fileName = $request->file('file')->getClientOriginalName();
        }

        $message = GroupMessage::create([
            'group_id'  => $groupId,
            'user_id'   => $user->id,
            'message'   => $data['message'],
            'file_path' => $filePath,
            'file_name' => $fileName,
        ]);

        $message->load('user:id,name,profile_image');

        broadcast(new GroupMessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message
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
    public function pinMessage(Request $request, $messageId)
    {
        $message = GroupMessage::with('group')->findOrFail($messageId);
        $user = $request->user();

        abort_unless(
            $message->group->owner_id === $user->id,
            403,
            'Only the group leader can pin messages'
        );

        $message->update([
            'is_pinned' => true
        ]);

        return response()->json(['success' => true]);
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

