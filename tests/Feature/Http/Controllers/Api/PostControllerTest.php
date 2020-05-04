<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Post;

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

    public function test_show()
    {
        //Create a fake post
        $post = factory(Post::class)->create();

        $response = $this->json('GET', "/api/posts/$post->id");

        // The JSON response should contains this structure
        $response->assertJsonStructure(
            ['id', 'title', 'created_at', 'updated_at']
        );

        //Verify status 200 and the right information in the post
        $response->assertStatus(200)
            ->assertJson(['title' => $post->title]);
    }

    public function test_404_show()
    {
        $response = $this->json('GET', '/api/posts/100');

        //Verify status 200 and the right information in the post
        $response->assertStatus(404);
    }

    public function test_update()
    {
        $this->withoutExceptionHandling();
        //Create a post
        $post = factory(Post::class)->create();

        $response = $this->putJson(
            "/api/posts/$post->id",
            ['title' => 'New title']
        );

        // The JSON response should contains this structure
        $response->assertJsonStructure(
            ['id', 'title', 'created_at', 'updated_at']
        );
        //The title should appear in the response
        $response->assertJson(['title' => 'New title']);
        //Resource created
        $response->assertStatus(200);

        //Confirm the data in the database
        $this->assertDatabaseHas('posts', ['title' => 'New title']);
    }

    public function test_delete()
    {
        $post = factory(Post::class)->create();
        //DELETE method request
        $response = $this->deleteJson("/api/posts/$post->id");
        //Test nothing is in the response
        $response->assertSee(null)
            ->assertStatus(204); //No content

        //Test the element was removed from the db
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_index()
    {
        factory(Post::class, 5)->create();
        $response = $this->getJson('/api/posts');
        // The JSON response should contains this structure
        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]
        )->assertStatus(200);
    }
}
