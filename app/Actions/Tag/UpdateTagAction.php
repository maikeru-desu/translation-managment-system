<?php

declare(strict_types=1);

namespace App\Actions\Tag;

use App\Models\Tag;
use Illuminate\Support\Facades\DB;

final class UpdateTagAction
{
    /**
     * Update an existing tag.
     */
    public function handle(Tag $tag, array $data): Tag
    {
        return DB::transaction(function () use ($tag, $data) {
            $tag->update($data);

            return $tag->fresh();
        });
    }
}
