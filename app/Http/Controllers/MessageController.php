<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreMessageRequest;
use App\Services\MessageService;
use App\Models\Message;
use App\Services\AutoReplyService;
class MessageController extends Controller
{
    public function index(int $id): JsonResponse
    {
        $user = auth('api')->user();

        $conversation = $user->conversations()
            ->findOrFail($id);

        $messages = $conversation->messages()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Messages retrieved successfully.',
            'data' => MessageResource::collection($messages),
        ]);
    }
   public function store(StoreMessageRequest $request, int $id,MessageService $messageService): JsonResponse {
        $user = auth('api')->user();
        $conversation = $user->conversations()
            ->findOrFail($id);
       

        $result = $messageService->send(  $conversation, $request->content);
        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => [
                'user_message' => new MessageResource(
                    $result['user_message']
                ),
                'bot_message' => new MessageResource(
                    $result['bot_message']
                ),
            ],
        ], 201);
    }
}