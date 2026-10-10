<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAutoReplyRuleRequest;
use App\Http\Requests\UpdateAutoReplyRuleRequest;
use App\Http\Resources\AutoReplyRuleResource;
use App\Models\AutoReplyRule;
use App\Http\Requests\UpdateFallbackRuleRequest;
use Illuminate\Http\JsonResponse;

class AutoReplyRuleController extends Controller
{
    public function index()
    {
        $rules = AutoReplyRule::latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'Auto reply rules retrieved successfully.',
            'data' => AutoReplyRuleResource::collection($rules),
        ]);
    }

    public function store(StoreAutoReplyRuleRequest $request): JsonResponse {
        $rule = AutoReplyRule::create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Auto reply rule created successfully.',
            'data' => new AutoReplyRuleResource($rule),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $rule = AutoReplyRule::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Auto reply rule retrieved successfully.',
            'data' => new AutoReplyRuleResource($rule),
        ]);
    }

    public function update( UpdateAutoReplyRuleRequest $request, int $id  ): JsonResponse {
        $rule = AutoReplyRule::findOrFail($id);

        $rule->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Auto reply rule updated successfully.',
            'data' => new AutoReplyRuleResource($rule->refresh()),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $rule = AutoReplyRule::findOrFail($id);

        $rule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Auto reply rule deleted successfully.',
            'data' => null,
        ]);
    }

    public function toggle(int $id): JsonResponse
    {
        $rule = AutoReplyRule::findOrFail($id);

        $rule->update([
            'is_active' => !$rule->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => $rule->is_active
                ? 'Auto reply rule activated successfully.'
                : 'Auto reply rule deactivated successfully.',
            'data' => new AutoReplyRuleResource($rule->refresh()),
        ]);
    }
    public function fallback(): JsonResponse
{
    $rule = AutoReplyRule::query()
        ->whereJsonLength('keywords', 0)
        ->firstOrFail();

    return response()->json([
        'success' => true,
        'message' => 'Fallback rule retrieved successfully.',
        'data' => new AutoReplyRuleResource($rule),
    ]);
}
public function updateFallback( UpdateFallbackRuleRequest $request): JsonResponse {
    $rule = AutoReplyRule::query()
        ->whereJsonLength('keywords', 0)
        ->firstOrFail();

    $rule->update($request->validated());

    return response()->json([
        'success' => true,
        'message' => 'Fallback rule updated successfully.',
        'data' => new AutoReplyRuleResource($rule->refresh()),
    ]);
}
}