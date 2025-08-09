<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class TestTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Translation::unguard();

        $locales = $this->ensureLocalesExist();
        $tags = $this->ensureTagsExist();

        $count = 500;
        $chunkSize = 1000;
        $chunks = (int) ceil($count / $chunkSize);

        for ($i = 0; $i < $chunks; $i++) {
            $this->seedChunk($locales, $tags, $chunkSize, $i * $chunkSize);
        }

        Translation::reguard();
    }

    /**
     * Ensure we have some locales to work with.
     */
    private function ensureLocalesExist(): Collection
    {
        if (Locale::count() === 0) {

            $locales = [
                ['code' => 'en', 'name' => 'English', 'is_active' => true],
                ['code' => 'es', 'name' => 'Spanish', 'is_active' => true],
                ['code' => 'fr', 'name' => 'French', 'is_active' => true],
                ['code' => 'de', 'name' => 'German', 'is_active' => true],
                ['code' => 'ja', 'name' => 'Japanese', 'is_active' => true],
            ];

            foreach ($locales as $locale) {
                Locale::create($locale);
            }
        }

        return Locale::select('id')->get();
    }

    /**
     * Ensure we have some tags to work with.
     */
    private function ensureTagsExist(): Collection
    {
        if (Tag::count() === 0) {

            $tags = [
                ['name' => 'frontend', 'description' => 'Frontend translations'],
                ['name' => 'backend', 'description' => 'Backend translations'],
                ['name' => 'error', 'description' => 'Error messages'],
                ['name' => 'navigation', 'description' => 'Navigation items'],
                ['name' => 'form', 'description' => 'Form labels and messages'],
                ['name' => 'notification', 'description' => 'Notification messages'],
                ['name' => 'email', 'description' => 'Email content'],
                ['name' => 'product', 'description' => 'Product related content'],
            ];

            foreach ($tags as $tag) {
                Tag::create($tag);
            }
        }

        return Tag::select('id')->get();
    }

    /**
     * Seed a chunk of translations.
     */
    private function seedChunk(Collection $locales, Collection $tags, int $chunkSize, int $offset): void
    {
        $pivotData = [];

        DB::transaction(function () use ($locales, $tags, $chunkSize, $offset, &$pivotData) {
            for ($i = 0; $i < $chunkSize; $i++) {
                $index = $offset + $i;
                $localeId = $locales->random()->id;

                $translation = [
                    'locale_id' => $localeId,
                    'key' => "translation.key.{$index}",
                    'content' => "This is test translation content for key {$index}. It simulates a realistic translation string with varying length and content.",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $translationId = DB::table('translations')->insertGetId($translation);

                $numTags = rand(0, 3);
                if ($numTags > 0) {
                    $randomTags = $tags->random($numTags);

                    foreach ($randomTags as $tag) {
                        $pivotData[] = [
                            'translation_id' => $translationId,
                            'tag_id' => $tag->id,
                        ];
                    }
                }
            }

            if (! empty($pivotData)) {
                foreach (array_chunk($pivotData, 500) as $chunk) {
                    DB::table('translation_tag')->insert($chunk);
                }
            }
        });
    }
}
