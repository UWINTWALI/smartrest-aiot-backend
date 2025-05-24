<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Send message; supports Patient⇄Doctor or Customer⇄Support threads.
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|uuid|exists:users,user_id',
            'title' => 'nullable|string',
            'body' => 'required|string',
            'type' => 'required|in:alert,chat,promo',
        ]);
        
        $sender = $request->user();
        $recipientId = $request->recipient_id;
        
        // Check if recipient exists
        $recipient = User::where('user_id', $recipientId)->first();
        if (!$recipient) {
            return response()->json([
                'message' => 'Recipient not found'
            ], 404);
        }
        
        // Patients can only message their doctors
        if ($sender->isPatient()) {
            $canMessage = $sender->patientProfile->doctors()
                ->where('doctor_id', $recipientId)
                ->exists();
                
            if (!$canMessage) {
                return response()->json([
                    'message' => 'You can only message your assigned doctors'
                ], 403);
            }
        }
        
        // Doctors can only message their patients
        if ($sender->isDoctor()) {
            $canMessage = $sender->doctorProfile->patients()
                ->where('patient_id', $recipientId)
                ->exists();
                
            if (!$canMessage) {
                return response()->json([
                    'message' => 'You can only message your assigned patients'
                ], 403);
            }
        }
        
        // Create the message
        $message = Message::create([
            'sender_id' => $sender->user_id,
            'recipient_id' => $recipientId,
            'title' => $request->title,
            'body' => $request->body,
            'type' => $request->type,
            'is_read' => false,
            'sent_at' => now(),
        ]);
        
        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message
        ], 201);
    }
    
    /**
     * Fetch or poll a specific thread.
     */
    public function getThread(Request $request, $conversationId)
    {
        $user = $request->user();
        
        // Determine if conversationId is a user_id or a message_id
        if (strpos($conversationId, 'msg_') === 0) {
            // It's a message_id
            $messageId = substr($conversationId, 4);
            $message = Message::where('message_id', $messageId)
                ->where(function($query) use ($user) {
                    $query->where('sender_id', $user->user_id)
                          ->orWhere('recipient_id', $user->user_id);
                })
                ->firstOrFail();
                
            $otherUserId = ($message->sender_id == $user->user_id) 
                ? $message->recipient_id 
                : $message->sender_id;
        } else {
            // It's a user_id (conversation partner)
            $otherUserId = $conversationId;
        }
        
        // Get messages between these two users
        $messages = Message::where(function($query) use ($user, $otherUserId) {
                $query->where(function($q) use ($user, $otherUserId) {
                    $q->where('sender_id', $user->user_id)
                      ->where('recipient_id', $otherUserId);
                })->orWhere(function($q) use ($user, $otherUserId) {
                    $q->where('sender_id', $otherUserId)
                      ->where('recipient_id', $user->user_id);
                });
            })
            ->orderBy('sent_at', 'desc')
            ->paginate(50);
        
        // Mark unread messages as read
        $unreadMessages = $messages->filter(function($message) use ($user) {
            return $message->recipient_id === $user->user_id && !$message->is_read;
        });
        
        if ($unreadMessages->count() > 0) {
            Message::whereIn('message_id', $unreadMessages->pluck('message_id'))
                ->update(['is_read' => true]);
        }
        
        return response()->json([
            'conversation_with' => $otherUserId,
            'messages' => $messages,
        ]);
    }
    
    /**
     * Get unread alerts for current user.
     */
    public function getNotifications(Request $request)
    {
        $user = $request->user();
        
        $notifications = Message::where('recipient_id', $user->user_id)
            ->where('is_read', false)
            ->where('type', 'alert')
            ->orderBy('sent_at', 'desc')
            ->get();
            
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $notifications->count(),
        ]);
    }
    
    /**
     * Mark alert as read / handled.
     */
    public function acknowledgeNotification(Request $request, $id)
    {
        $user = $request->user();
        
        $notification = Message::where('message_id', $id)
            ->where('recipient_id', $user->user_id)
            ->where('type', 'alert')
            ->firstOrFail();
            
        $notification->update(['is_read' => true]);
        
        return response()->json([
            'message' => 'Notification acknowledged',
        ]);
    }
}
