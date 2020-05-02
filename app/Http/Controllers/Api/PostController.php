<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Post;
use App\Http\Requests\PostRequest;

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
        //
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
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }
}
