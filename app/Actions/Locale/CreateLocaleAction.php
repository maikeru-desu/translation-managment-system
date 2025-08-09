<?php

declare(strict_types=1);

namespace App\Actions\Locale;

use App\Models\Locale;
use Illuminate\Support\Facades\DB;

final class CreateLocaleAction
{
    /**
     * Create a new locale.
     */
    public function handle(array $data): Locale
    {
        return DB::transaction(function () use ($data) {
            if (! isset($data['is_active'])) {
                $data['is_active'] = true;
            }

            return Locale::create($data);
        });
    }
}
