<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/post/list",
     *     tags={"posts"},
     *     summary="Get all Posts",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="index",
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status values that needed to be considered for filter",
     *         required=true,
     *         explode=true,
     *         @OA\Schema(
     *             default="available",
     *             type="string",
     *             enum={"available", "pending", "sold"},
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     )
     * )
     */
    public function index()
    {
        $posts = Post::list();
        return response()->json(['success' => true, 'posts' => $posts]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'required|string',
        ]);

        if ($request->hasFile('image')) {
            $images = $request->file('image');
            $imagePaths = [];

            foreach ($images as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $imagePaths[] = $imageName;
            }

        }
        if ($request->hasFile('video')) {
            $videos = $request->file('video');
            $videoPaths = [];
            foreach ($videos as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $videoPaths[] = $imageName;
            }
        }
        $user_id = Auth::id();
        $post = Post::create([
            'user_id' => $user_id,
            'title' => $request->title,
            'text' => $request->text,
            'image' => json_encode($imagePaths),
            'video' => json_encode($videoPaths),

        ]);
        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post,
        ], 201);
    }
    public function show(string $id)
    {
        $post = Post::find($id);
        if ($post) {
            return response()->json(['success' => true, 'data' => $post], 200);
        } else {

            return response()->json(['success' => false, 'message' => 'This post do not have'], 400);
        }
    }
    public function destroy(string $id)
    {
        $user_id = Auth::id();
        $post = Post::where('id', $id)
            ->where('user_id', $user_id)
            ->first();

        if ($post) {

            $post->delete();
            return response()->json([
                'success' => true,
                'data' => true,
                'message' => 'Post deleted successfully'
            ], 200);

        } else {
            return response()->json([
                'success' => false,
                'data' => true,
                'message' => 'Post not found'
            ], 400);
        }
    }
    public function updateImage(Request $request, $id)
    {
        $user_id = Auth::id();
        $post = Post::where('id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (!$post) {
            return response()->json([
                'message' => 'Post not found or you are not authorized to update this post.',
            ], 404);
        }

        $imagePaths = [];
        $videoPaths = [];

        if ($request->hasFile('image')) {
            $images = $request->file('image');
            foreach ($images as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $imageName);
                $imagePaths[] = $imageName;
            }
            $post->image = json_encode($imagePaths);
        }

        if ($request->hasFile('video')) {
            $videos = $request->file('video');
            foreach ($videos as $video) {
                $videoName = time() . '_' . $video->getClientOriginalName();
                $video->move(public_path('images'), $videoName);
                $videoPaths[] = $videoName;
            }
            $post->video = json_encode($videoPaths);
        }

        $post->save();
        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post,
        ], 200);
    }
    public function update(Request $request, $id)
    {
        $user_id = Auth::id();
        $post = Post::where('id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (!$post) {
            return response()->json([
                'message' => 'Post not found or It is not your post',
            ], 404);
        }

        $data = $request->json()->all();

        if (isset($data['title'])) {
            $post->title = $data['title'];
        }

        if (isset($data['text'])) {
            $post->text = $data['text'];
        }

        $post->save();

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post,
        ], 200);
    }

}




