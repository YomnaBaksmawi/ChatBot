<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConversationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function store(): JsonResponse
    {
        $user = auth('api')->user();

        $conversation = $user->conversations()->create([
            'status' => 'OPEN',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation created successfully.',
            'data' => new ConversationResource($conversation),
        ], 201);
    }

    public function index(): JsonResponse
    {
        $user = auth('api')->user();

        $conversations = $user->conversations()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Conversations retrieved successfully.',
            'data' => ConversationResource::collection($conversations),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $user = auth('api')->user();

        $conversation = $user->conversations()
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Conversation retrieved successfully.',
            'data' => new ConversationResource($conversation),
        ]);
    }

    public function close(int $id): JsonResponse
    {
        $user = auth('api')->user();

        $conversation = $user->conversations()
            ->findOrFail($id);

        if ($conversation->status === 'CLOSED') {
            return response()->json([
                'success' => false,
                'message' => 'Conversation is already closed.',
                'errors' => null,
            ], 422);
        }

        $conversation->update([
            'status' => 'CLOSED',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation closed successfully.',
            'data' => new ConversationResource($conversation),
        ]);
    }
}