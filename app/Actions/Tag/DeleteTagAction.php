<?php

declare(strict_types=1);

namespace App\Actions\Tag;

use App\Models\Tag;
use Illuminate\Support\Facades\DB;

final class DeleteTagAction
{
    /**
     * Delete a tag and remove it from any translations.
     */
    public function handle(Tag $tag): bool
    {
        return DB::transaction(function () use ($tag) {
            $tag->translations()->detach();

            return $tag->delete();
        });
    }
}
