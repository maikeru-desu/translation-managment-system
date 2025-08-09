<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Tag\CreateTagAction;
use App\Actions\Tag\DeleteTagAction;
use App\Actions\Tag\UpdateTagAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

final class TagController extends Controller
{
    /**
     * Display a listing of the tags.
     */
    public function index(): JsonResponse
    {
        $tags = Tag::all();

        return response()->json([
            'data' => $tags,
        ], 200);
    }

    /**
     * Store a newly created tag in storage.
     */
    public function store(CreateTagRequest $request, CreateTagAction $action): JsonResponse
    {
        $tag = $action->handle($request->validated());

        return response()->json([
            'data' => $tag,
            'message' => 'Tag created successfully',
        ], 201);
    }

    /**
     * Display the specified tag.
     */
    public function show(Tag $tag): JsonResponse
    {
        return response()->json([
            'data' => $tag,
        ], 200);
    }

    /**
     * Update the specified tag in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag, UpdateTagAction $action): JsonResponse
    {
        $updatedTag = $action->handle($tag, $request->validated());

        return response()->json([
            'data' => $updatedTag,
            'message' => 'Tag updated successfully',
        ], 200);
    }

    /**
     * Remove the specified tag from storage.
     */
    public function destroy(Tag $tag, DeleteTagAction $action): JsonResponse
    {
        $action->handle($tag);

        return response()->json([
            'message' => 'Tag deleted successfully',
        ], 200);
    }
}
