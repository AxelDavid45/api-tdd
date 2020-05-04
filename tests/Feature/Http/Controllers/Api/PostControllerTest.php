<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Post;
use App\User;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    // Tests the store method
    public function test_store()
    {
        $user = factory(User::class)->create();
        //$this->withoutExceptionHandling();
        $response = $this->actingAs($user, 'api')->postJson('/api/posts', ['title' => 'test title']);

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
        $user = factory(User::class)->create();
        //Create a request
        $response = $this->actingAs($user, 'api')->postJson(
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
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user, 'api')->json('GET', "/api/posts/$post->id");

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
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json('GET', '/api/posts/100');

        //Verify status 200 and the right information in the post
        $response->assertStatus(404);
    }

    public function test_update()
    {
        $user = factory(User::class)->create();
        //$this->withoutExceptionHandling();
        //Create a post
        $post = factory(Post::class)->create();

        $response = $this->actingAs($user, 'api')->putJson(
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
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();
        //DELETE method request
        $response = $this->actingAs($user, 'api')->deleteJson("/api/posts/$post->id");
        //Test nothing is in the response
        $response->assertSee(null)
            ->assertStatus(204); //No content

        //Test the element was removed from the db
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_index()
    {
        $user = factory(User::class)->create();
        factory(Post::class, 5)->create();
        $response = $this->actingAs($user, 'api')->getJson('/api/posts');
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

    public function test_guest()
    {
        //Check that the user is not authorize
        $this->getJson('api/posts')->assertStatus(401);
        $this->postJson('api/posts')->assertStatus(401);
        $this->putJson('api/posts/1000')->assertStatus(401);
        $this->deleteJson('api/posts/1000')->assertStatus(401);
        $this->getJson('api/posts/1000')->assertStatus(401);
    }
}
