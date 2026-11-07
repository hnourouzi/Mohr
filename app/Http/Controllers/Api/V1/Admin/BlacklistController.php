<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBlacklistWordRequest;
use App\Http\Resources\BlacklistWordResource;
use App\Models\BlacklistWord;
use Illuminate\Http\JsonResponse;

class BlacklistController extends Controller
{
    public function index(): JsonResponse
    {
        $words = BlacklistWord::query()->latest()->paginate(20);

        return BlacklistWordResource::collection($words)->response();
    }

    public function store(StoreBlacklistWordRequest $request): JsonResponse
    {
        $word = BlacklistWord::create($request->validated());

        return (new BlacklistWordResource($word))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(BlacklistWord $blacklistWord): JsonResponse
    {
        $blacklistWord->delete();

        return response()->json(['message' => 'کلمه حذف شد.']);
    }
}
