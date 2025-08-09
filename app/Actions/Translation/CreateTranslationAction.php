<?php

declare(strict_types=1);

namespace App\Actions\Translation;

use App\Models\Translation;
use Illuminate\Support\Facades\DB;

final class CreateTranslationAction
{
    /**
     * Create a new translation.
     */
    public function handle(array $data): Translation
    {
        return DB::transaction(function () use ($data) {
            $tags = $data['tags'] ?? [];
            unset($data['tags']);

            $translation = Translation::create($data);

            if (! empty($tags)) {
                $translation->tags()->attach($tags);
            }

            return $translation->load(['locale', 'tags']);
        });
    }
}
