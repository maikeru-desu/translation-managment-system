<?php

declare(strict_types=1);

namespace App\Actions\Translation;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class SearchTranslationAction
{
    /**
     * Search for translations based on various criteria.
     */
    public function handle(array $filters): LengthAwarePaginator
    {
        $query = Translation::with(['locale', 'tags']);

        if (! empty($filters['locale_ids'])) {
            $query->whereIn('locale_id', $filters['locale_ids']);
        }

        if (isset($filters['key']) && $filters['key'] !== '') {
            $query->where('key', 'like', '%'.$filters['key'].'%');
        }

        if (isset($filters['content']) && $filters['content'] !== '') {
            $query->where('content', 'like', '%'.$filters['content'].'%');
        }

        if (! empty($filters['tag_ids'])) {
            $tagIds = $filters['tag_ids'];
            $query->whereHas('tags', function (Builder $query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            });
        }

        $perPage = $filters['per_page'] ?? 20;
        $page = $filters['page'] ?? 1;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
