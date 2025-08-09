<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class LargeTranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Translation::unguard();

        $locales = $this->ensureLocalesExist();
        $tags = $this->ensureTagsExist();

        $this->command->info('Starting to seed translations...');

        $count = $this->command->ask('How many translations would you like to seed?', 100000);

        $chunkSize = 1000;
        $chunks = (int) ceil($count / $chunkSize);

        $bar = $this->command->getOutput()->createProgressBar($chunks);
        $bar->start();

        for ($i = 0; $i < $chunks; $i++) {
            $this->seedChunk($locales, $tags, $chunkSize, $i * $chunkSize);
            $bar->advance();
        }

        $bar->finish();
        $this->command->info("\nSeeded {$count} translations successfully!");

        Translation::reguard();
    }

    /**
     * Ensure we have some locales to work with.
     */
    private function ensureLocalesExist(): Collection
    {
        if (Locale::count() === 0) {
            $this->command->info('Creating default locales...');

            // Create some common locales
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
            $this->command->info('Creating default tags...');

            // Create some common tags
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
     *
     * @param  Collection  $locales
     * @param  Collection  $tags
     * @param  int  $chunkSize
     * @param  int  $offset
     */
    private function seedChunk($locales, $tags, $chunkSize, $offset): void
    {
        $translations = [];
        $pivotData = [];

        // Using DB::transaction for better performance
        DB::transaction(function () use ($locales, $tags, $chunkSize, $offset, &$translations, &$pivotData) {
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

                // Insert the translation
                $translationId = DB::table('translations')->insertGetId($translation);

                // Randomly associate with 0-3 tags
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

            // Bulk insert tag associations if we have any
            if (! empty($pivotData)) {
                // Insert in smaller chunks to avoid query size limits
                foreach (array_chunk($pivotData, 500) as $chunk) {
                    DB::table('translation_tag')->insert($chunk);
                }
            }
        });
    }
}
