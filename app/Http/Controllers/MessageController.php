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
     * 
     * @OA\Post(
     *     path="/messages",
     *     summary="Send a new message",
     *     description="Send a message to a recipient (patient to doctor, doctor to patient, customer to support)",
     *     operationId="sendMessage",
     *     tags={"Messaging"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"recipient_id", "body", "type"},
     *             @OA\Property(property="recipient_id", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *             @OA\Property(property="title", type="string", example="Sleep Report Discussion"),
     *             @OA\Property(property="body", type="string", example="I've reviewed your latest sleep report and have some recommendations."),
     *             @OA\Property(property="type", type="string", enum={"alert", "chat", "promo"}, example="chat")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Message sent successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Not authorized to message this recipient",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You can only message your assigned doctors")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Recipient not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Recipient not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
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
     * 
     * @OA\Get(
     *     path="/messages/thread/{conversationId}",
     *     summary="Get conversation thread",
     *     description="Retrieve message thread between the current user and another user or from a specific message",
     *     operationId="getMessageThread",
     *     tags={"Messaging"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="conversationId",
     *         in="path",
     *         required=true,
     *         description="User ID or message ID (prefixed with 'msg_')",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message thread",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="messages",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="message_id", type="string", example="msg_12345"),
     *                     @OA\Property(property="sender_id", type="string", format="uuid"),
     *                     @OA\Property(property="recipient_id", type="string", format="uuid"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="body", type="string"),
     *                     @OA\Property(property="type", type="string", enum={"alert", "chat", "promo"}),
     *                     @OA\Property(property="is_read", type="boolean"),
     *                     @OA\Property(property="sent_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Thread not found"
     *     )
     * )
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
     * 
     * @OA\Get(
     *     path="/messages/notifications",
     *     summary="Get user notifications",
     *     description="Retrieve unread notifications and alerts for the current user",
     *     operationId="getUserNotifications",
     *     tags={"Messaging"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User notifications",
     *         @OA\JsonContent(
     *             @OA\Property(property="unread_count", type="integer", example=5),
     *             @OA\Property(
     *                 property="notifications",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="message_id", type="string"),
     *                     @OA\Property(property="sender_id", type="string", format="uuid"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="body", type="string"),
     *                     @OA\Property(property="type", type="string", enum={"alert", "chat", "promo"}),
     *                     @OA\Property(property="sent_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
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
     * 
     * @OA\Put(
     *     path="/messages/notifications/{id}",
     *     summary="Mark notification as read",
     *     description="Mark a specific notification as read",
     *     operationId="markNotificationRead",
     *     tags={"Messaging"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Notification ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification acknowledged",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Notification acknowledged")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - Not your notification"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notification not found"
     *     )
     * )
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
