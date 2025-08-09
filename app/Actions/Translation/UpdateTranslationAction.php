<?php

declare(strict_types=1);

namespace App\Actions\Translation;

use App\Models\Translation;
use Illuminate\Support\Facades\DB;

final class UpdateTranslationAction
{
    /**
     * Update an existing translation.
     */
    public function handle(Translation $translation, array $data): Translation
    {
        return DB::transaction(function () use ($translation, $data) {
            $tags = null;

            if (array_key_exists('tags', $data)) {
                $tags = $data['tags'];
                unset($data['tags']);
            }

            $translation->update($data);

            if ($tags !== null) {
                $translation->tags()->sync($tags);
            }

            return $translation->fresh(['locale', 'tags']);
        });
    }
}
