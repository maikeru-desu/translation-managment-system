<?php

declare(strict_types=1);

namespace App\Actions\Translation;

use App\Models\Translation;
use Illuminate\Support\Facades\DB;

final class DeleteTranslationAction
{
    /**
     * Delete a translation and its tag associations.
     */
    public function handle(Translation $translation): bool
    {
        return DB::transaction(function () use ($translation) {
            $translation->tags()->detach();

            return $translation->delete();
        });
    }
}
