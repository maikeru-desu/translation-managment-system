<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Translation\CreateTranslationAction;
use App\Actions\Translation\DeleteTranslationAction;
use App\Actions\Translation\ExportTranslationAction;
use App\Actions\Translation\SearchTranslationAction;
use App\Actions\Translation\UpdateTranslationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Translation\CreateTranslationRequest;
use App\Http\Requests\Translation\ExportTranslationRequest;
use App\Http\Requests\Translation\SearchTranslationRequest;
use App\Http\Requests\Translation\UpdateTranslationRequest;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TranslationController extends Controller
{
    /**
     * Display a listing of the translations.
     */
    public function index(Request $request): JsonResponse
    {
        $translations = Translation::with(['locale', 'tags'])->paginate(20);

        return response()->json($translations, 200);
    }

    /**
     * Store a newly created translation in storage.
     */
    public function store(CreateTranslationRequest $request, CreateTranslationAction $action): JsonResponse
    {
        $translation = $action->handle($request->validated());

        return response()->json([
            'data' => $translation,
            'message' => 'Translation created successfully',
        ], 201);
    }

    /**
     * Display the specified translation.
     */
    public function show(Translation $translation): JsonResponse
    {
        $translation->load(['locale', 'tags']);

        return response()->json([
            'data' => $translation,
        ], 200);
    }

    /**
     * Update the specified translation in storage.
     */
    public function update(UpdateTranslationRequest $request, Translation $translation, UpdateTranslationAction $action): JsonResponse
    {
        $updatedTranslation = $action->handle($translation, $request->validated());

        return response()->json([
            'data' => $updatedTranslation,
            'message' => 'Translation updated successfully',
        ], 200);
    }

    /**
     * Remove the specified translation from storage.
     */
    public function destroy(Translation $translation, DeleteTranslationAction $action): JsonResponse
    {
        $action->handle($translation);

        return response()->json([
            'message' => 'Translation deleted successfully',
        ], 200);
    }

    /**
     * Search for translations based on various criteria.
     */
    public function search(SearchTranslationRequest $request, SearchTranslationAction $action): JsonResponse
    {
        $results = $action->handle($request->validated());

        return response()->json($results, 200);
    }

    /**
     * Export translations in a format suitable for frontend consumption.
     */
    public function export(ExportTranslationRequest $request, ExportTranslationAction $action): JsonResponse
    {
        $result = $action->handle($request->validated());

        return response()->json($result, 200);
    }
}
