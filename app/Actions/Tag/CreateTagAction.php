<?php

declare(strict_types=1);

namespace App\Actions\Tag;

use App\Models\Tag;
use Illuminate\Support\Facades\DB;

final class CreateTagAction
{
    /**
     * Create a new tag.
     */
    public function handle(array $data): Tag
    {
        return DB::transaction(function () use ($data) {
            return Tag::create($data);
        });
    }
}
