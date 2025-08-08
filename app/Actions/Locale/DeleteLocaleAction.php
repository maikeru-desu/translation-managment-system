<?php

declare(strict_types=1);

namespace App\Actions\Locale;

use App\Models\Locale;
use Illuminate\Support\Facades\DB;

final class DeleteLocaleAction
{
    /**
     * Delete a locale and its associated translations.
     */
    public function handle(Locale $locale): bool
    {
        return DB::transaction(function () use ($locale) {
            return $locale->delete();
        });
    }
}
