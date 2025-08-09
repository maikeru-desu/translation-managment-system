<?php

declare(strict_types=1);

namespace App\Actions\Translation;

use App\Models\Locale;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

final class ExportTranslationAction
{
    /**
     * Export translations optimized for frontend consumption.
     */
    public function handle(array $filters): array
    {
        $cacheKey = $this->generateCacheKey($filters);

        return Cache::remember($cacheKey, 300, function () use ($filters) {
            return $this->fetchTranslations($filters);
        });
    }

    /**
     * Fetch translations based on filters.
     */
    private function fetchTranslations(array $filters): array
    {
        $localeQuery = Locale::query()->where('is_active', true);

        if (! empty($filters['locale_ids'])) {
            $localeQuery->whereIn('id', $filters['locale_ids']);
        }

        $locales = $localeQuery->get();

        $translationQuery = Translation::query()
            ->whereIn('locale_id', $locales->pluck('id'))
            ->select('translations.id', 'translations.locale_id', 'translations.key', 'translations.content');

        if (! empty($filters['tag_ids'])) {
            $translationQuery->whereHas('tags', function (Builder $query) use ($filters) {
                $query->whereIn('tags.id', $filters['tag_ids']);
            });
        }

        $result = [];
        $translationQuery->with('locale:id,code');

        $translationQuery->chunk(1000, function ($translations) use (&$result) {
            foreach ($translations as $translation) {
                $locale = $translation->locale->code;

                if (! isset($result[$locale])) {
                    $result[$locale] = [];
                }

                $this->setNestedValue($result[$locale], $translation->key, $translation->content);
            }
        });

        return $result;
    }

    /**
     * Set a nested value using dot notation key.
     */
    private function setNestedValue(array &$array, string $key, $value): void
    {
        $keys = explode('.', $key);
        $current = &$array;

        foreach ($keys as $k) {
            if (! isset($current[$k]) || ! is_array($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }

        $current = $value;
    }

    /**
     * Generate a cache key based on filters.
     */
    private function generateCacheKey(array $filters): string
    {
        $params = [
            'locale' => $filters['locale'] ?? ($filters['locale_ids'] ?? 'all'),
            'tags' => $filters['tags'] ?? ($filters['tag_ids'] ?? []),
        ];

        return 'translations_export_'.md5(json_encode($params));
    }
}
