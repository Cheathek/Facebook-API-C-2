<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $posts = Post::list();
        return response()->json(['success' => true, 'posts' => $posts]);
    }

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Post::store($request);
        return response()->json([
            'success' => true,
            'data' => true,
            'message' => 'post created successfully'
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $post = Post::find($id);
        return response()->json(['success' => true, 'data' => $post], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        Post::store($request, $id);
        return response()->json([
            'success' => true,
            'data' => true,
            'message' => 'post update successfully'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        $post->delete();
        return response()->json([
            'success' => true,
            'data' => true,
            'message' => 'post delete successfully'
        ], 200);
    }
}




