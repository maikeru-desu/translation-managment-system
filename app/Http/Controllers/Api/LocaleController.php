<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\Locale\CreateLocaleAction;
use App\Actions\Locale\DeleteLocaleAction;
use App\Actions\Locale\UpdateLocaleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Locale\CreateLocaleRequest;
use App\Http\Requests\Locale\UpdateLocaleRequest;
use App\Models\Locale;
use Illuminate\Http\JsonResponse;

final class LocaleController extends Controller
{
    /**
     * Display a listing of the locales.
     */
    public function index(): JsonResponse
    {
        $locales = Locale::all();

        return response()->json([
            'data' => $locales,
        ], 200);
    }

    /**
     * Store a newly created locale in storage.
     */
    public function store(CreateLocaleRequest $request, CreateLocaleAction $action): JsonResponse
    {
        $locale = $action->handle($request->validated());

        return response()->json([
            'data' => $locale,
            'message' => 'Locale created successfully',
        ], 201);
    }

    /**
     * Display the specified locale.
     */
    public function show(Locale $locale): JsonResponse
    {
        return response()->json([
            'data' => $locale,
        ], 200);
    }

    /**
     * Update the specified locale in storage.
     */
    public function update(UpdateLocaleRequest $request, Locale $locale, UpdateLocaleAction $action): JsonResponse
    {
        $updatedLocale = $action->handle($locale, $request->validated());

        return response()->json([
            'data' => $updatedLocale,
            'message' => 'Locale updated successfully',
        ], 200);
    }

    /**
     * Remove the specified locale from storage.
     */
    public function destroy(Locale $locale, DeleteLocaleAction $action): JsonResponse
    {
        $action->handle($locale);

        return response()->json([
            'message' => 'Locale deleted successfully',
        ], 200);
    }
}
