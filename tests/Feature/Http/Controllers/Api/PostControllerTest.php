<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    // Tests the store method
    public function test_store()
    {
        $this->withoutExceptionHandling();
        $response = $this->postJson('/api/posts', ['title' => 'test title']);

        // The JSON response should contains this structure
        $response->assertJsonStructure(
            ['id', 'title', 'created_at', 'updated_at']
        );
        //The title should appear in the response
        $response->assertJson(['title' => 'test title']);
        //Resource created
        $response->assertStatus(201);

        //Confirm the data in the database
        $this->assertDatabaseHas('posts', ['title' => 'test title']);
    }

    public function test_no_emptyTitles()
    {
        //Create a request
        $response = $this->postJson(
            '/api/posts',
            ['title' => '']
        );

        //Verify the HTTP code
        $response->assertStatus(422)
            ->assertJsonValidationErrors('title');

    }
}
