<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\User;
use Database\Seeders\TestTranslationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

final class LocaleApiTest extends TestCase
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
    public function it_can_list_all_locales()
    {
        $locales = Locale::all();
        $localeCount = $locales->count();

        $response = $this->getJson('/api/locales');

        $response->assertStatus(200);
        $response->assertJsonCount($localeCount, 'data');

        foreach ($locales as $locale) {
            $response->assertJsonFragment(['code' => $locale->code]);
        }
    }

    /** @test */
    public function it_can_create_a_new_locale()
    {
        $data = [
            'code' => 'pt',
            'name' => 'Portuguese',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/locales', $data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'code' => 'pt',
            'name' => 'Portuguese',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('locales', $data);
    }

    /** @test */
    public function it_validates_locale_creation_data()
    {
        $response = $this->postJson('/api/locales', [
            'code' => '',
            'name' => str_repeat('a', 101),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['code', 'name']);
    }

    /** @test */
    public function it_can_show_a_specific_locale()
    {
        $locale = Locale::first();

        $response = $this->getJson("/api/locales/{$locale->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $locale->id,
            'code' => $locale->code,
            'name' => $locale->name,
        ]);
    }

    /** @test */
    public function it_can_update_a_locale()
    {
        $locale = Locale::first();
        $originalCode = $locale->code;

        $data = [
            'name' => 'Updated Name',
            'is_active' => false,
        ];

        $response = $this->putJson("/api/locales/{$locale->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Updated Name',
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('locales', [
            'id' => $locale->id,
            'code' => $originalCode,
            'name' => 'Updated Name',
            'is_active' => false,
        ]);
    }

    /** @test */
    public function it_can_delete_a_locale()
    {
        $locale = Locale::create([
            'code' => 'zz',
            'name' => 'Test Locale for Deletion',
            'is_active' => true,
        ]);

        $response = $this->deleteJson("/api/locales/{$locale->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Locale deleted successfully',
        ]);

        $this->assertDatabaseMissing('locales', [
            'id' => $locale->id,
        ]);
    }
}
