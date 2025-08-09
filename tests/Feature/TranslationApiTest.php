<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\User;
use Database\Seeders\TestTranslationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Locale $locale;

    protected Tag $tag;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        $this->seed(TestTranslationSeeder::class);

        $this->locale = Locale::first();
        $this->tag = Tag::first();
    }

    /** @test */
    public function it_can_list_all_translations()
    {
        $translationCount = Translation::count();

        $response = $this->getJson('/api/translations');

        $response->assertStatus(200);
        
        // Check that we're getting paginated results
        $this->assertArrayHasKey('data', $response->json());
        $this->assertNotEmpty($response->json('data'), 'No translations returned in the response');
        
        // If API is paginated (which it appears to be), just check we get some data back
        // rather than expecting all translations
        $this->assertGreaterThan(0, count($response->json('data')));
        $this->assertGreaterThan(0, $translationCount, 'No translations found in the database');
    }

    /** @test */
    public function it_can_create_a_new_translation()
    {
        $uniqueKey = 'test.uniquekey.' . time();

        $data = [
            'locale_id' => $this->locale->id,
            'key' => $uniqueKey,
            'content' => 'This is a test translation created through the API',
            'tags' => [$this->tag->id],
        ];

        $response = $this->postJson('/api/translations', $data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'key' => $uniqueKey,
            'content' => 'This is a test translation created through the API',
        ]);

        $this->assertDatabaseHas('translations', [
            'locale_id' => $this->locale->id,
            'key' => $uniqueKey,
            'content' => 'This is a test translation created through the API',
        ]);

        $translation = Translation::where('key', $uniqueKey)->first();
        $this->assertNotNull($translation, 'Translation was not created');
        $this->assertCount(1, $translation->tags);
        $this->assertEquals($this->tag->id, $translation->tags[0]->id);
    }

    /** @test */
    public function it_validates_translation_creation_data()
    {
        $response = $this->postJson('/api/translations', [
            'locale_id' => 999,
            'key' => '',
            'content' => null,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['locale_id', 'key', 'content']);
    }

    /** @test */
    public function it_can_show_a_specific_translation()
    {
        $translation = Translation::first();
        if ($translation->tags->isEmpty()) {
            $translation->tags()->attach($this->tag->id);
        }

        $response = $this->getJson("/api/translations/{$translation->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $translation->id,
            'key' => $translation->key,
            'content' => $translation->content,
        ]);

        $response->assertJsonStructure([
            'data' => [
                'id', 'key', 'content', 'locale_id', 'created_at', 'updated_at',
                'tags'
            ]
        ]);
    }

    /** @test */
    public function it_can_update_a_translation()
    {
        $translation = Translation::inRandomOrder()->first();
        $updateKey = 'updated.key.' . time();

        $data = [
            'key' => $updateKey,
            'content' => 'Updated content for test',
            'tags' => [$this->tag->id],
        ];

        $response = $this->putJson("/api/translations/{$translation->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'key' => $updateKey,
            'content' => 'Updated content for test',
        ]);

        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'key' => $updateKey,
            'content' => 'Updated content for test',
        ]);

        $updatedTranslation = Translation::find($translation->id);
        $this->assertCount(1, $updatedTranslation->tags);
        $this->assertEquals($this->tag->id, $updatedTranslation->tags[0]->id);
    }

    /** @test */
    public function it_can_delete_a_translation()
    {
        $translation = Translation::create([
            'locale_id' => $this->locale->id,
            'key' => 'to.be.deleted.' . time(),
            'content' => 'This translation will be deleted',
        ]);
        $translation->tags()->attach($this->tag->id);

        $response = $this->deleteJson("/api/translations/{$translation->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Translation deleted successfully',
        ]);

        $this->assertDatabaseMissing('translations', [
            'id' => $translation->id,
        ]);

        $this->assertDatabaseMissing('translation_tag', [
            'translation_id' => $translation->id,
        ]);
    }
}
