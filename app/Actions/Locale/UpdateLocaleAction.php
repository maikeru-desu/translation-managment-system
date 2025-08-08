<?php

declare(strict_types=1);

namespace App\Actions\Locale;

use App\Models\Locale;
use Illuminate\Support\Facades\DB;

final class UpdateLocaleAction
{
    /**
     * Update an existing locale.
     */
    public function handle(Locale $locale, array $data): Locale
    {
        return DB::transaction(function () use ($locale, $data) {
            $locale->update($data);

            return $locale->fresh();
        });
    }
}
