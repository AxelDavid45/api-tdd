<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }


    /*
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->post->paginate());
    }

    //Store a new resource
    public function store(PostRequest $request)
    {
        $post = $this->post->create($request->all());
        return response()->json($post, 201);
    }

    /*
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response()->json($post);
    }

    /*
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        $post->update($request->all());
        return response()->json($post, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     *
     * @return JsonResponse
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(null, 204);
    }
}
