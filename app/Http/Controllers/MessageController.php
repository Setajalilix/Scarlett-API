<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'required|string',
        ]);
        $sender = auth()->user();
        $receiver = User::findOrFail($request->receiver_id);


        if ($sender->role === 'user' && $receiver->role === 'user') {
            return response()->json([
                'message' => 'چت بین کاربران عادی مجاز نیست'
            ], 403);
        }
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'body' => $request->body,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return new MessageResource($message);
    }

    public function history($userId)
    {
        $me = auth()->user();
        $other = User::findOrFail($userId);

        if ($me->role === 'user' && $other->role === 'user') {
            return response()->json([
                'message' => 'دسترسی غیرمجاز'
            ], 403);
        }
        $messages = Message::where(function ($q) use ($userId) {
            $q->where('sender_id', auth()->id())
                ->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
                ->where('receiver_id', auth()->id());
        })
            ->orderBy('created_at')
            ->get();

        return MessageResource::collection($messages);
    }

}
