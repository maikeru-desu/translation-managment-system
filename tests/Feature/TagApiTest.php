<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Database\Seeders\TestTranslationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class TagApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        $this->seed(TestTranslationSeeder::class);
    }

    /** @test */
    public function it_can_list_all_tags()
    {
        $tags = Tag::all();
        $tagCount = $tags->count();

        $response = $this->getJson('/api/tags');

        $response->assertStatus(200);
        $response->assertJsonCount($tagCount, 'data');

        foreach ($tags->take(3) as $tag) {
            $response->assertJsonFragment(['name' => $tag->name]);
        }
    }

    /** @test */
    public function it_can_create_a_new_tag()
    {
        $data = [
            'name' => 'new-unique-test-tag',
            'description' => 'Test tag created via API',
        ];

        $response = $this->postJson('/api/tags', $data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'name' => 'new-unique-test-tag',
            'description' => 'Test tag created via API',
        ]);

        $this->assertDatabaseHas('tags', $data);
    }

    /** @test */
    public function it_validates_tag_creation_data()
    {
        $response = $this->postJson('/api/tags', [
            'name' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }


    public function it_enforces_unique_tag_names()
    {
        $existingTag = Tag::first();

        $response = $this->postJson('/api/tags', [
            'name' => $existingTag->name,
            'description' => 'Another description',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function it_can_show_a_specific_tag()
    {
        $tag = Tag::first();

        $response = $this->getJson("/api/tags/{$tag->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $tag->id,
            'name' => $tag->name,
            'description' => $tag->description,
        ]);
    }

    /** @test */
    public function it_can_update_a_tag()
    {
        $tag = Tag::first();

        $data = [
            'name' => 'updated-test-tag',
            'description' => 'Updated description for test tag',
        ];

        $response = $this->putJson("/api/tags/{$tag->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'updated-test-tag',
            'description' => 'Updated description for test tag',
        ]);

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'updated-test-tag',
            'description' => 'Updated description for test tag',
        ]);
    }

    /** @test */
    public function it_can_delete_a_tag()
    {
        $tag = Tag::create([
            'name' => 'tag-to-be-deleted',
            'description' => 'This tag will be deleted',
        ]);

        $response = $this->deleteJson("/api/tags/{$tag->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Tag deleted successfully',
        ]);

        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }
}
